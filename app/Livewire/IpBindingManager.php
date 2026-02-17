<?php

namespace App\Livewire;

use App\Models\Router;
use Livewire\Component;

class IpBindingManager extends Component
{
    public $routers = [];
    public $selectedRouter = '';
    public $bindings = [];
    public $dhcpLeases = [];
    public $searchMac = '';
    public $isLoading = false;
    public $showTab = 'bindings'; // bindings, leases

    // Create Modal
    public $showModal = false;
    public $formData = [
        'mac_address' => '',
        'type' => 'bypassed',
        'comment' => '',
    ];

    public function mount()
    {
        $this->routers = Router::where('is_active', true)->get();
        
        if ($this->routers->isNotEmpty()) {
            $this->selectedRouter = $this->routers->first()->id;
            $this->loadData();
        }
    }

    public function updatedSelectedRouter()
    {
        $this->loadData();
    }

    public function loadData()
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
                // IP Bindings
                $this->bindings = collect($api->getIpBindings())->map(function ($binding) {
                    return [
                        'id' => $binding['.id'] ?? '',
                        'mac_address' => $binding['mac-address'] ?? 'N/A',
                        'address' => $binding['address'] ?? 'N/A',
                        'to_address' => $binding['to-address'] ?? '',
                        'type' => $binding['type'] ?? 'regular',
                        'comment' => $binding['comment'] ?? '',
                        'disabled' => ($binding['disabled'] ?? 'false') === 'true',
                    ];
                })->when($this->searchMac, function ($collection) {
                    return $collection->filter(function ($binding) {
                        return stripos($binding['mac_address'], $this->searchMac) !== false ||
                               stripos($binding['comment'], $this->searchMac) !== false;
                    });
                })->values()->toArray();

                // DHCP Leases
                $this->dhcpLeases = collect($api->getDhcpLeases())->map(function ($lease) {
                    return [
                        'id' => $lease['.id'] ?? '',
                        'address' => $lease['address'] ?? 'N/A',
                        'mac_address' => $lease['mac-address'] ?? 'N/A',
                        'host_name' => $lease['host-name'] ?? 'N/A',
                        'server' => $lease['server'] ?? 'N/A',
                        'status' => $lease['status'] ?? 'N/A',
                        'expires_after' => $lease['expires-after'] ?? 'N/A',
                        'dynamic' => ($lease['dynamic'] ?? 'false') === 'true',
                    ];
                })->when($this->searchMac, function ($collection) {
                    return $collection->filter(function ($lease) {
                        return stripos($lease['mac_address'], $this->searchMac) !== false ||
                               stripos($lease['host_name'], $this->searchMac) !== false;
                    });
                })->values()->toArray();

                $api->disconnect();
            }
        } catch (\Exception $e) {
            session()->flash('error', 'রাউটারে কানেক্ট করা যায়নি: ' . $e->getMessage());
        }

        $this->isLoading = false;
    }

    public function openModal($mac = '')
    {
        $this->formData = [
            'mac_address' => $mac,
            'type' => 'bypassed',
            'comment' => '',
        ];
        $this->showModal = true;
    }

    public function save()
    {
        $router = Router::find($this->selectedRouter);
        if (!$router) return;

        try {
            $api = $router->getApi();
            
            if ($api->connect()) {
                $result = $api->addIpBinding(
                    $this->formData['mac_address'],
                    $this->formData['type'],
                    $this->formData['comment']
                );

                if ($result) {
                    session()->flash('message', 'IP Binding যোগ করা হয়েছে!');
                    $this->showModal = false;
                } else {
                    session()->flash('error', 'যোগ করতে ব্যর্থ!');
                }

                $api->disconnect();
            }
        } catch (\Exception $e) {
            session()->flash('error', 'ত্রুটি: ' . $e->getMessage());
        }

        $this->loadData();
    }

    public function delete($id)
    {
        $router = Router::find($this->selectedRouter);
        if (!$router) return;

        try {
            $api = $router->getApi();
            
            if ($api->connect()) {
                $response = $api->command('/ip/hotspot/ip-binding/remove', ['=.id=' . $id]);
                
                if (isset($response[0]) && $response[0] === '!done') {
                    session()->flash('message', 'Binding ডিলিট হয়েছে!');
                } else {
                    session()->flash('error', 'ডিলিট করতে ব্যর্থ!');
                }

                $api->disconnect();
            }
        } catch (\Exception $e) {
            session()->flash('error', 'ত্রুটি: ' . $e->getMessage());
        }

        $this->loadData();
    }

    public function bypassMac($mac)
    {
        $this->formData['mac_address'] = $mac;
        $this->formData['type'] = 'bypassed';
        $this->formData['comment'] = 'DHCP থেকে Bypass';
        $this->save();
    }

    public function blockMac($mac)
    {
        $this->formData['mac_address'] = $mac;
        $this->formData['type'] = 'blocked';
        $this->formData['comment'] = 'DHCP থেকে Block';
        $this->save();
    }

    public function render()
    {
        return view('livewire.ip-binding-manager');
    }
}
