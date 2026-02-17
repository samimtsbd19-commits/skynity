<?php

namespace App\Livewire;

use App\Models\Router;
use App\Models\StockUser;
use App\Models\Voucher;
use App\Models\Sale;
use App\Models\SessionLog;
use Livewire\Component;

class Dashboard extends Component
{
    public $selectedRouter = null;
    public $routers = [];
    public $systemInfo = [];
    public $activeUsers = [];
    public $stats = [];
    public $stockStats = [];

    public function mount()
    {
        $this->routers = Router::where('is_active', true)->get();
        
        if ($this->routers->isNotEmpty()) {
            $this->selectedRouter = $this->routers->first()->id;
            $this->loadDashboardData();
        }
    }

    public function updatedSelectedRouter()
    {
        $this->loadDashboardData();
    }

    public function loadDashboardData()
    {
        if (!$this->selectedRouter) return;

        $router = Router::find($this->selectedRouter);
        if (!$router) return;

        // স্ট্যাটিস্টিক্স
        $this->stats = [
            'total_vouchers' => Voucher::where('router_id', $this->selectedRouter)->count(),
            'unused_vouchers' => Voucher::where('router_id', $this->selectedRouter)
                ->where('status', 'unused')->count(),
            'used_vouchers' => Voucher::where('router_id', $this->selectedRouter)
                ->where('status', 'used')->count(),
            'today_sales' => Sale::whereHas('voucher', function($q) {
                $q->where('router_id', $this->selectedRouter);
            })->whereDate('created_at', today())->sum('amount'),
            'month_sales' => Sale::whereHas('voucher', function($q) {
                $q->where('router_id', $this->selectedRouter);
            })->whereMonth('created_at', now()->month)->sum('amount'),
        ];

        // Stock User Stats
        $this->stockStats = [
            'available' => StockUser::where('router_id', $this->selectedRouter)
                ->where('status', 'available')->count(),
            'assigned' => StockUser::where('router_id', $this->selectedRouter)
                ->where('status', 'assigned')->count(),
            'expired' => StockUser::where('router_id', $this->selectedRouter)
                ->where('status', 'expired')->count(),
            'total' => StockUser::where('router_id', $this->selectedRouter)->count(),
        ];

        // MikroTik থেকে লাইভ ডাটা
        $this->loadLiveData($router);
    }

    public function loadLiveData(Router $router)
    {
        try {
            $api = $router->getApi();
            
            if ($api->connect()) {
                // সিস্টেম ইনফো
                $resource = $api->getSystemResource();
                $this->systemInfo = [
                    'identity' => $api->getIdentity(),
                    'board_name' => $resource['board-name'] ?? 'N/A',
                    'version' => $resource['version'] ?? 'N/A',
                    'uptime' => $resource['uptime'] ?? 'N/A',
                    'cpu_load' => $resource['cpu-load'] ?? 0,
                    'free_memory' => $this->formatBytes($resource['free-memory'] ?? 0),
                    'total_memory' => $this->formatBytes($resource['total-memory'] ?? 0),
                    'free_hdd' => $this->formatBytes($resource['free-hdd-space'] ?? 0),
                ];

                // অ্যাক্টিভ ইউজার
                $this->activeUsers = $api->getActiveUsers();
                $this->stats['active_users'] = count($this->activeUsers);

                $api->disconnect();
            }
        } catch (\Exception $e) {
            $this->systemInfo = ['error' => $e->getMessage()];
        }
    }

    public function kickUser($userId)
    {
        $router = Router::find($this->selectedRouter);
        if (!$router) return;

        $api = $router->getApi();
        if ($api->connect()) {
            $api->kickActiveUser($userId);
            $api->disconnect();
            $this->loadLiveData($router);
            session()->flash('message', 'ইউজার কিক করা হয়েছে!');
        }
    }

    public function refreshData()
    {
        $this->loadDashboardData();
    }

    private function formatBytes($bytes): string
    {
        $bytes = (int) $bytes;
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes >= 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    public function render()
    {
        return view('livewire.dashboard');
    }
}
