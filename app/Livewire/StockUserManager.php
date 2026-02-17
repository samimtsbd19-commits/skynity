<?php

namespace App\Livewire;

use App\Models\Router;
use App\Models\Package;
use App\Models\StockUser;
use App\Services\MikrotikApi;
use Livewire\Component;
use Livewire\WithPagination;

class StockUserManager extends Component
{
    use WithPagination;

    public $selectedRouter = '';
    public $selectedPackage = '';
    public $generateCount = 50;
    public $usernamePrefix = 'SKY';
    public $passwordLength = 8;
    public $speedLimit = '10M/10M';
    public $validityDays = 30;
    public $profile = 'default';
    
    public $showGenerateModal = false;
    public $generating = false;
    public $generationProgress = 0;
    public $generationMessage = '';
    
    public $search = '';
    public $statusFilter = '';
    
    protected $rules = [
        'selectedRouter' => 'required',
        'generateCount' => 'required|integer|min:1|max:500',
        'usernamePrefix' => 'required|string|max:10',
        'passwordLength' => 'required|integer|min:6|max:16',
        'speedLimit' => 'required|string',
        'validityDays' => 'required|integer|min:1|max:365',
    ];

    public function openGenerateModal()
    {
        $this->showGenerateModal = true;
    }

    public function generate()
    {
        $this->validate();
        
        $this->generating = true;
        $this->generationProgress = 0;
        $this->generationMessage = 'শুরু হচ্ছে...';
        
        $router = Router::find($this->selectedRouter);
        if (!$router) {
            session()->flash('error', 'রাউটার পাওয়া যায়নি!');
            $this->generating = false;
            return;
        }
        
        $created = 0;
        $failed = 0;
        
        // Connect to MikroTik
        $api = $router->getApi();
        $connected = $api->connect();
        
        for ($i = 0; $i < $this->generateCount; $i++) {
            $username = $this->usernamePrefix . strtoupper($this->generateRandomString(6));
            $password = $this->generateRandomString($this->passwordLength);
            
            // Check if username exists
            if (StockUser::where('username', $username)->exists()) {
                $i--;
                continue;
            }
            
            // Create in MikroTik if connected (disabled)
            $createdInMikrotik = false;
            if ($connected) {
                $createdInMikrotik = $api->addDisabledHotspotUser(
                    $username,
                    $password,
                    $this->profile,
                    'Stock User - SKYNITY'
                );
            }
            
            // Create in database
            StockUser::create([
                'router_id' => $this->selectedRouter,
                'package_id' => $this->selectedPackage ?: null,
                'username' => $username,
                'password' => $password,
                'profile' => $this->profile,
                'speed_limit' => $this->speedLimit,
                'validity_days' => $this->validityDays,
                'status' => 'available',
                'created_in_mikrotik_at' => $createdInMikrotik ? now() : null,
            ]);
            
            $created++;
            $this->generationProgress = round(($i + 1) / $this->generateCount * 100);
            $this->generationMessage = "তৈরি হয়েছে: {$created}/{$this->generateCount}";
        }
        
        if ($connected) {
            $api->disconnect();
        }
        
        $this->generating = false;
        $this->showGenerateModal = false;
        session()->flash('message', "{$created}টি স্টক ইউজার তৈরি হয়েছে!" . ($connected ? ' (MikroTik এ যোগ হয়েছে)' : ' (শুধু ডাটাবেসে)'));
    }

    public function deleteUser($id)
    {
        $user = StockUser::find($id);
        if ($user && $user->status === 'available') {
            // Try to remove from MikroTik
            $router = $user->router;
            if ($router) {
                $api = $router->getApi();
                if ($api->connect()) {
                    // Remove user by name
                    $users = $api->command('/ip/hotspot/user/print', ['?name=' . $user->username]);
                    $filtered = [];
                    foreach ($users as $item) {
                        if (is_array($item) && isset($item['.id'])) {
                            $api->command('/ip/hotspot/user/remove', ['=.id=' . $item['.id']]);
                        }
                    }
                    $api->disconnect();
                }
            }
            
            $user->delete();
            session()->flash('message', 'ইউজার মুছে ফেলা হয়েছে!');
        }
    }

    public function deleteAllAvailable()
    {
        $count = StockUser::where('status', 'available')->count();
        StockUser::where('status', 'available')->delete();
        session()->flash('message', "{$count}টি স্টক ইউজার মুছে ফেলা হয়েছে!");
    }

    private function generateRandomString($length)
    {
        $characters = '0123456789ABCDEFGHJKLMNPQRSTUVWXYZ';
        $string = '';
        for ($i = 0; $i < $length; $i++) {
            $string .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $string;
    }

    public function render()
    {
        $routers = Router::where('is_active', true)->get();
        $packages = Package::where('is_active', true)->get();
        
        $query = StockUser::with(['router', 'package']);
        
        if ($this->selectedRouter) {
            $query->where('router_id', $this->selectedRouter);
        }
        
        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }
        
        if ($this->search) {
            $query->where('username', 'like', '%' . $this->search . '%');
        }
        
        $stockUsers = $query->orderBy('created_at', 'desc')->paginate(20);
        
        // Stats
        $stats = [
            'total' => StockUser::count(),
            'available' => StockUser::where('status', 'available')->count(),
            'assigned' => StockUser::where('status', 'assigned')->count(),
            'expired' => StockUser::where('status', 'expired')->count(),
        ];
        
        return view('livewire.stock-user-manager', compact('routers', 'packages', 'stockUsers', 'stats'));
    }
}
