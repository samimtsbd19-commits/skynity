<?php

namespace App\Livewire;

use App\Models\Router;
use Livewire\Component;

class TrafficMonitor extends Component
{
    public $routers = [];
    public $selectedRouter = '';
    public $interfaceData = [];
    public $queueData = [];
    public $systemData = [
        'cpu_load' => 0,
        'cpu_count' => 1,
        'free_memory' => 'N/A',
        'total_memory' => 'N/A',
        'memory_percent' => 0,
        'free_hdd' => 'N/A',
        'total_hdd' => 'N/A',
        'hdd_percent' => 0,
        'uptime' => 'N/A',
        'version' => 'N/A',
        'board_name' => 'N/A',
        'architecture' => 'N/A',
    ];
    public $isLoading = false;

    public function mount()
    {
        $this->routers = Router::where('is_active', true)->get();
        
        if ($this->routers->isNotEmpty()) {
            $this->selectedRouter = $this->routers->first()->id;
            $this->loadTrafficData();
        }
    }

    public function updatedSelectedRouter()
    {
        $this->loadTrafficData();
    }

    public function loadTrafficData()
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
                // Interface stats
                $interfaces = $api->command('/interface/print');
                $this->interfaceData = collect($this->filterData($interfaces))->map(function ($iface) {
                    return [
                        'name' => $iface['name'] ?? 'N/A',
                        'type' => $iface['type'] ?? 'N/A',
                        'rx_byte' => $this->formatBytes($iface['rx-byte'] ?? 0),
                        'tx_byte' => $this->formatBytes($iface['tx-byte'] ?? 0),
                        'rx_packet' => $iface['rx-packet'] ?? 0,
                        'tx_packet' => $iface['tx-packet'] ?? 0,
                        'running' => ($iface['running'] ?? 'false') === 'true',
                        'disabled' => ($iface['disabled'] ?? 'false') === 'true',
                    ];
                })->values()->toArray();

                // Simple queue stats
                $queues = $api->command('/queue/simple/print');
                $this->queueData = collect($this->filterData($queues))->map(function ($queue) {
                    $rates = explode('/', $queue['rate'] ?? '0/0');
                    return [
                        'name' => $queue['name'] ?? 'N/A',
                        'target' => $queue['target'] ?? 'N/A',
                        'max_limit' => $queue['max-limit'] ?? 'N/A',
                        'upload_rate' => $this->formatBits($rates[0] ?? 0) . '/s',
                        'download_rate' => $this->formatBits($rates[1] ?? 0) . '/s',
                        'bytes' => $queue['bytes'] ?? '0/0',
                        'disabled' => ($queue['disabled'] ?? 'false') === 'true',
                    ];
                })->values()->toArray();

                // System resources
                $resource = $api->getSystemResource();
                $this->systemData = [
                    'cpu_load' => $resource['cpu-load'] ?? 0,
                    'cpu_count' => $resource['cpu-count'] ?? 1,
                    'free_memory' => $this->formatBytes($resource['free-memory'] ?? 0),
                    'total_memory' => $this->formatBytes($resource['total-memory'] ?? 0),
                    'memory_percent' => $this->calculateMemoryPercent($resource),
                    'free_hdd' => $this->formatBytes($resource['free-hdd-space'] ?? 0),
                    'total_hdd' => $this->formatBytes($resource['total-hdd-space'] ?? 0),
                    'hdd_percent' => $this->calculateHddPercent($resource),
                    'uptime' => $resource['uptime'] ?? 'N/A',
                    'version' => $resource['version'] ?? 'N/A',
                    'board_name' => $resource['board-name'] ?? 'N/A',
                    'architecture' => $resource['architecture-name'] ?? 'N/A',
                ];

                $api->disconnect();
            }
        } catch (\Exception $e) {
            session()->flash('error', 'রাউটারে কানেক্ট করা যায়নি: ' . $e->getMessage());
        }

        $this->isLoading = false;
    }

    private function filterData(array $response): array
    {
        return array_filter($response, function ($item) {
            return is_array($item);
        });
    }

    private function calculateMemoryPercent($resource): int
    {
        $total = (int)($resource['total-memory'] ?? 1);
        $free = (int)($resource['free-memory'] ?? 0);
        return $total > 0 ? round((($total - $free) / $total) * 100) : 0;
    }

    private function calculateHddPercent($resource): int
    {
        $total = (int)($resource['total-hdd-space'] ?? 1);
        $free = (int)($resource['free-hdd-space'] ?? 0);
        return $total > 0 ? round((($total - $free) / $total) * 100) : 0;
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

    private function formatBits($bits): string
    {
        $bits = (int) $bits;
        if ($bits >= 1000000000) {
            return number_format($bits / 1000000000, 2) . ' Gbps';
        } elseif ($bits >= 1000000) {
            return number_format($bits / 1000000, 2) . ' Mbps';
        } elseif ($bits >= 1000) {
            return number_format($bits / 1000, 2) . ' Kbps';
        }
        return $bits . ' bps';
    }

    public function render()
    {
        return view('livewire.traffic-monitor');
    }
}
