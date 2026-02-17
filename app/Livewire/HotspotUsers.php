<?php

namespace App\Livewire;

use App\Models\Router;
use App\Models\Voucher;
use Livewire\Component;
use Livewire\WithPagination;

class HotspotUsers extends Component
{
    use WithPagination;

    public $routers = [];
    public $selectedRouter = '';
    public $users = [];
    public $searchUser = '';
    public $filterProfile = '';
    public $profiles = [];
    public $isLoading = false;

    // Create/Edit Modal
    public $showModal = false;
    public $editMode = false;
    public $editId = '';
    public $formData = [
        'name' => '',
        'password' => '',
        'profile' => '',
        'comment' => '',
        'limit_uptime' => '',
        'limit_bytes' => '',
    ];

    // Stats
    public $totalUsers = 0;
    public $activeCount = 0;
    public $disabledCount = 0;

    public function mount()
    {
        $this->routers = Router::where('is_active', true)->get();
        
        if ($this->routers->isNotEmpty()) {
            $this->selectedRouter = $this->routers->first()->id;
            $this->loadUsers();
        }
    }

    public function updatedSelectedRouter()
    {
        $this->loadProfiles();
        $this->loadUsers();
    }

    public function loadProfiles()
    {
        $router = Router::find($this->selectedRouter);
        if (!$router) return;

        try {
            $api = $router->getApi();
            if ($api->connect()) {
                $this->profiles = $api->getHotspotProfiles();
                $api->disconnect();
            }
        } catch (\Exception $e) {
            $this->profiles = [];
        }
    }

    public function loadUsers()
    {
        if (!$this->selectedRouter) return;

        $this->isLoading = true;
        $this->loadProfiles();
        
        $router = Router::find($this->selectedRouter);
        if (!$router) {
            $this->isLoading = false;
            return;
        }

        try {
            $api = $router->getApi();
            
            if ($api->connect()) {
                $hotspotUsers = $api->getHotspotUsers();
                
                $this->users = collect($hotspotUsers)->map(function ($user) {
                    return [
                        'id' => $user['.id'] ?? '',
                        'name' => $user['name'] ?? '',
                        'password' => $user['password'] ?? '',
                        'profile' => $user['profile'] ?? 'default',
                        'comment' => $user['comment'] ?? '',
                        'limit_uptime' => $user['limit-uptime'] ?? '',
                        'limit_bytes' => $user['limit-bytes-total'] ?? '',
                        'uptime' => $user['uptime'] ?? '0s',
                        'bytes_in' => $user['bytes-in'] ?? 0,
                        'bytes_out' => $user['bytes-out'] ?? 0,
                        'disabled' => ($user['disabled'] ?? 'false') === 'true',
                    ];
                })->when($this->searchUser, function ($collection) {
                    return $collection->filter(function ($user) {
                        return stripos($user['name'], $this->searchUser) !== false ||
                               stripos($user['comment'], $this->searchUser) !== false;
                    });
                })->when($this->filterProfile, function ($collection) {
                    return $collection->filter(function ($user) {
                        return $user['profile'] === $this->filterProfile;
                    });
                })->values()->toArray();

                // Stats
                $this->totalUsers = count($hotspotUsers);
                $this->activeCount = collect($hotspotUsers)->where('disabled', '!=', 'true')->count();
                $this->disabledCount = collect($hotspotUsers)->where('disabled', 'true')->count();

                $api->disconnect();
            }
        } catch (\Exception $e) {
            session()->flash('error', 'রাউটারে কানেক্ট করা যায়নি: ' . $e->getMessage());
        }

        $this->isLoading = false;
    }

    public function openCreateModal()
    {
        $this->resetForm();
        $this->editMode = false;
        $this->showModal = true;
    }

    public function openEditModal($user)
    {
        $this->editMode = true;
        $this->editId = $user['id'];
        $this->formData = [
            'name' => $user['name'],
            'password' => $user['password'],
            'profile' => $user['profile'],
            'comment' => $user['comment'],
            'limit_uptime' => $user['limit_uptime'],
            'limit_bytes' => $user['limit_bytes'],
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
                if ($this->editMode) {
                    $result = $api->editHotspotUser($this->editId, [
                        'name' => $this->formData['name'],
                        'password' => $this->formData['password'],
                        'profile' => $this->formData['profile'],
                        'comment' => $this->formData['comment'],
                        'limit-uptime' => $this->formData['limit_uptime'],
                        'limit-bytes-total' => $this->formData['limit_bytes'],
                    ]);
                    $message = 'ইউজার আপডেট হয়েছে!';
                } else {
                    $result = $api->addHotspotUser(
                        $this->formData['name'],
                        $this->formData['password'],
                        $this->formData['profile'],
                        $this->formData['comment'],
                        $this->formData['limit_uptime'],
                        $this->formData['limit_bytes']
                    );
                    $message = 'নতুন ইউজার তৈরি হয়েছে!';
                }

                if ($result) {
                    session()->flash('message', $message);
                    $this->showModal = false;
                    $this->resetForm();
                } else {
                    session()->flash('error', 'অপারেশন ব্যর্থ হয়েছে!');
                }

                $api->disconnect();
            }
        } catch (\Exception $e) {
            session()->flash('error', 'ত্রুটি: ' . $e->getMessage());
        }

        $this->loadUsers();
    }

    public function delete($id)
    {
        $router = Router::find($this->selectedRouter);
        if (!$router) return;

        try {
            $api = $router->getApi();
            
            if ($api->connect()) {
                if ($api->removeHotspotUser($id)) {
                    session()->flash('message', 'ইউজার ডিলিট হয়েছে!');
                } else {
                    session()->flash('error', 'ডিলিট করতে ব্যর্থ!');
                }
                $api->disconnect();
            }
        } catch (\Exception $e) {
            session()->flash('error', 'ত্রুটি: ' . $e->getMessage());
        }

        $this->loadUsers();
    }

    public function toggleUser($id, $currentStatus)
    {
        $router = Router::find($this->selectedRouter);
        if (!$router) return;

        try {
            $api = $router->getApi();
            
            if ($api->connect()) {
                $api->toggleHotspotUser($id, !$currentStatus);
                $api->disconnect();
            }
        } catch (\Exception $e) {
            session()->flash('error', 'ত্রুটি: ' . $e->getMessage());
        }

        $this->loadUsers();
    }

    public function syncFromMikrotik()
    {
        $router = Router::find($this->selectedRouter);
        if (!$router) return;

        try {
            $api = $router->getApi();
            
            if ($api->connect()) {
                $mikrotikUsers = $api->getHotspotUsers();
                $synced = 0;

                foreach ($mikrotikUsers as $user) {
                    $exists = Voucher::where('router_id', $this->selectedRouter)
                        ->where('username', $user['name'] ?? '')
                        ->exists();

                    if (!$exists && !empty($user['name'])) {
                        Voucher::create([
                            'router_id' => $this->selectedRouter,
                            'package_id' => 1,
                            'created_by' => auth()->id(),
                            'username' => $user['name'],
                            'password' => $user['password'] ?? '',
                            'status' => 'active',
                            'comment' => 'MikroTik থেকে সিঙ্ক করা হয়েছে',
                        ]);
                        $synced++;
                    }
                }

                session()->flash('message', "{$synced} টি ইউজার সিঙ্ক হয়েছে!");
                $api->disconnect();
            }
        } catch (\Exception $e) {
            session()->flash('error', 'ত্রুটি: ' . $e->getMessage());
        }
    }

    private function resetForm()
    {
        $this->formData = [
            'name' => '',
            'password' => '',
            'profile' => $this->profiles[0]['name'] ?? 'default',
            'comment' => '',
            'limit_uptime' => '',
            'limit_bytes' => '',
        ];
        $this->editId = '';
    }

    public function render()
    {
        return view('livewire.hotspot-users');
    }
}
