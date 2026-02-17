<?php

namespace App\Livewire;

use App\Models\Router;
use Livewire\Component;

class ActiveSessions extends Component
{
    public $routers = [];
    public $selectedRouter = '';
    public $sessions = [];
    public $searchUser = '';
    public $refreshInterval = 10;
    public $isLoading = false;
    
    // Stats
    public $totalSessions = 0;
    public $totalUpload = 0;
    public $totalDownload = 0;

    public function mount()
    {
        $this->routers = Router::where('is_active', true)->get();
        
        if ($this->routers->isNotEmpty()) {
            $this->selectedRouter = $this->routers->first()->id;
            $this->loadSessions();
        }
    }

    public function updatedSelectedRouter()
    {
        $this->loadSessions();
    }

    public function loadSessions()
    {
        if (!$this->selectedRouter) return;

        $this->isLoading = true;
        $router = Router::find($this->selectedRouter);
        
        if (!$router) {
            $this->isLoading = false;
            return;
        }

        try {
            $api = $router->getApi();
            
            if ($api->connect()) {
                $activeSessions = $api->getActiveUsers();
                
                // ফিল্টার এবং ফরম্যাট
                $this->sessions = collect($activeSessions)->map(function ($session) {
                    return [
                        'id' => $session['.id'] ?? '',
                        'user' => $session['user'] ?? 'Unknown',
                        'address' => $session['address'] ?? 'N/A',
                        'mac_address' => $session['mac-address'] ?? 'N/A',
                        'uptime' => $session['uptime'] ?? '0s',
                        'bytes_in' => $this->formatBytes($session['bytes-in'] ?? 0),
                        'bytes_out' => $this->formatBytes($session['bytes-out'] ?? 0),
                        'bytes_in_raw' => $session['bytes-in'] ?? 0,
                        'bytes_out_raw' => $session['bytes-out'] ?? 0,
                        'login_by' => $session['login-by'] ?? 'N/A',
                        'server' => $session['server'] ?? 'N/A',
                    ];
                })->when($this->searchUser, function ($collection) {
                    return $collection->filter(function ($session) {
                        return stripos($session['user'], $this->searchUser) !== false ||
                               stripos($session['mac_address'], $this->searchUser) !== false;
                    });
                })->values()->toArray();

                // স্ট্যাটস
                $this->totalSessions = count($this->sessions);
                $this->totalUpload = $this->formatBytes(collect($this->sessions)->sum('bytes_in_raw'));
                $this->totalDownload = $this->formatBytes(collect($this->sessions)->sum('bytes_out_raw'));

                $api->disconnect();
            }
        } catch (\Exception $e) {
            session()->flash('error', 'রাউটারে কানেক্ট করা যায়নি: ' . $e->getMessage());
        }

        $this->isLoading = false;
    }

    public function kickUser($sessionId)
    {
        $router = Router::find($this->selectedRouter);
        
        if (!$router) return;

        try {
            $api = $router->getApi();
            
            if ($api->connect()) {
                if ($api->kickActiveUser($sessionId)) {
                    session()->flash('message', 'ইউজার সফলভাবে কিক করা হয়েছে!');
                } else {
                    session()->flash('error', 'ইউজার কিক করতে ব্যর্থ!');
                }
                $api->disconnect();
            }
        } catch (\Exception $e) {
            session()->flash('error', 'ত্রুটি: ' . $e->getMessage());
        }

        $this->loadSessions();
    }

    public function kickAll()
    {
        $router = Router::find($this->selectedRouter);
        
        if (!$router) return;

        try {
            $api = $router->getApi();
            
            if ($api->connect()) {
                $kicked = 0;
                foreach ($this->sessions as $session) {
                    if ($api->kickActiveUser($session['id'])) {
                        $kicked++;
                    }
                }
                session()->flash('message', "{$kicked} জন ইউজার কিক করা হয়েছে!");
                $api->disconnect();
            }
        } catch (\Exception $e) {
            session()->flash('error', 'ত্রুটি: ' . $e->getMessage());
        }

        $this->loadSessions();
    }

    private function formatBytes($bytes): string
    {
        $bytes = (int) $bytes;
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        }
        return $bytes . ' B';
    }

    public function render()
    {
        return view('livewire.active-sessions');
    }
}
