<?php

namespace App\Livewire;

use App\Models\Router;
use App\Models\Package;
use Livewire\Component;

class ProfileSync extends Component
{
    public $routers = [];
    public $selectedRouter = '';
    public $mikrotikProfiles = [];
    public $localPackages = [];
    public $isLoading = false;
    public $syncedCount = 0;
    public $unsyncedCount = 0;

    // Create Profile Modal
    public $showModal = false;
    public $formData = [
        'name' => '',
        'rate_limit' => '',
        'shared_users' => '1',
        'session_timeout' => '',
        'keepalive_timeout' => '2m',
    ];

    // Local package edit/create form (for the right-side "লোকাল প্যাকেজ" table)
    public $editingId = null;
    public $formName = '';
    public $formProfile = '';
    public $formPrice = 0;
    public $formValidity = '';
    public $formValidityType = 'days';

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

        // Local packages
        $this->localPackages = Package::where('router_id', $this->selectedRouter)->get()->toArray();

        try {
            $api = $router->getApi();
            
            if ($api->connect()) {
                $profiles = $api->getHotspotProfiles();
                
                $this->mikrotikProfiles = collect($profiles)->map(function ($profile) {
                    return [
                        'id' => $profile['.id'] ?? '',
                        'name' => $profile['name'] ?? '',
                        'rate_limit' => $profile['rate-limit'] ?? 'N/A',
                        'shared_users' => $profile['shared-users'] ?? '1',
                        'session_timeout' => $profile['session-timeout'] ?? 'N/A',
                        'keepalive_timeout' => $profile['keepalive-timeout'] ?? 'N/A',
                        'idle_timeout' => $profile['idle-timeout'] ?? 'N/A',
                        'mac_cookie_timeout' => $profile['mac-cookie-timeout'] ?? 'N/A',
                        'synced' => $this->isProfileSynced($profile['name'] ?? ''),
                    ];
                })->values()->toArray();

                // Update counters
                $this->syncedCount = collect($this->mikrotikProfiles)->where('synced', true)->count();
                $this->unsyncedCount = collect($this->mikrotikProfiles)->where('synced', false)->count();

                $api->disconnect();
            }
        } catch (\Exception $e) {
            session()->flash('error', 'রাউটারে কানেক্ট করা যায়নি: ' . $e->getMessage());
        }

        $this->isLoading = false;
    }

    public function syncFromMikrotik()
    {
        $this->loadData();
        session()->flash('message', 'MikroTik থেকে প্রোফাইল লোড করা হয়েছে!');
    }

    public function isProfileSynced($profileName): bool
    {
        return Package::where('router_id', $this->selectedRouter)
            ->where('mikrotik_profile', $profileName)
            ->exists();
    }

    public function syncProfile($profileName, $rateLimit)
    {
        $exists = Package::where('router_id', $this->selectedRouter)
            ->where('mikrotik_profile', $profileName)
            ->exists();

        if ($exists) {
            session()->flash('error', 'এই প্রোফাইল আগেই সিঙ্ক করা আছে!');
            return;
        }

        Package::create([
            'router_id' => $this->selectedRouter,
            'name' => $profileName,
            'mikrotik_profile' => $profileName,
            'speed_limit' => $rateLimit !== 'N/A' ? $rateLimit : null,
            'price' => 0,
            'selling_price' => 0,
            'is_active' => true,
        ]);

        session()->flash('message', "'{$profileName}' প্রোফাইল সিঙ্ক হয়েছে!");
        $this->loadData();
    }

    public function syncAllProfiles()
    {
        $synced = 0;

        foreach ($this->mikrotikProfiles as $profile) {
            if (!$profile['synced'] && $profile['name'] !== 'default') {
                Package::create([
                    'router_id' => $this->selectedRouter,
                    'name' => $profile['name'],
                    'mikrotik_profile' => $profile['name'],
                    'speed_limit' => $profile['rate_limit'] !== 'N/A' ? $profile['rate_limit'] : null,
                    'price' => 0,
                    'selling_price' => 0,
                    'is_active' => true,
                ]);
                $synced++;
            }
        }

        session()->flash('message', "{$synced} টি প্রোফাইল সিঙ্ক হয়েছে!");
        $this->loadData();
    }

    public function openCreateModal()
    {
        $this->formData = [
            'name' => '',
            'rate_limit' => '',
            'shared_users' => '1',
            'session_timeout' => '',
            'keepalive_timeout' => '2m',
        ];
        $this->showModal = true;
    }

    // Package edit helpers for the Local Packages section
    public function editPackage($id)
    {
        $package = Package::find($id);
        if (!$package) return;

        $this->editingId = $package->id;
        $this->formName = $package->name ?? '';
        $this->formProfile = $package->mikrotik_profile ?? '';
        $this->formPrice = $package->price ?? 0;

        // Try to split validity like "30 days"
        $this->formValidity = '';
        $this->formValidityType = 'days';
        if (!empty($package->validity)) {
            $parts = explode(' ', $package->validity, 2);
            $this->formValidity = $parts[0] ?? '';
            $this->formValidityType = $parts[1] ?? 'days';
        }

        $this->showModal = true;
    }

    public function deletePackage($id)
    {
        Package::where('id', $id)->delete();
        session()->flash('message', 'প্যাকেজ ডিলিট হয়েছে!');
        $this->loadData();
    }

    public function savePackage()
    {
        $data = [
            'router_id' => $this->selectedRouter,
            'name' => $this->formName,
            'mikrotik_profile' => $this->formProfile,
            'validity' => trim(($this->formValidity ?: '') . ' ' . ($this->formValidityType ?: '')),
            'price' => $this->formPrice ?: 0,
            'selling_price' => $this->formPrice ?: 0,
            'is_active' => true,
        ];

        if ($this->editingId) {
            Package::where('id', $this->editingId)->update($data);
            session()->flash('message', 'প্যাকেজ আপডেট হয়েছে!');
        } else {
            Package::create($data);
            session()->flash('message', 'প্যাকেজ যোগ হয়েছে!');
        }

        $this->showModal = false;
        $this->editingId = null;
        $this->loadData();
    }

    public function createProfile()
    {
        $router = Router::find($this->selectedRouter);
        if (!$router) return;

        try {
            $api = $router->getApi();
            
            if ($api->connect()) {
                $attributes = [
                    '=name=' . $this->formData['name'],
                    '=shared-users=' . $this->formData['shared_users'],
                ];

                if ($this->formData['rate_limit']) {
                    $attributes[] = '=rate-limit=' . $this->formData['rate_limit'];
                }
                if ($this->formData['session_timeout']) {
                    $attributes[] = '=session-timeout=' . $this->formData['session_timeout'];
                }
                if ($this->formData['keepalive_timeout']) {
                    $attributes[] = '=keepalive-timeout=' . $this->formData['keepalive_timeout'];
                }

                $response = $api->command('/ip/hotspot/user/profile/add', $attributes);

                if (isset($response[0]) && $response[0] === '!done') {
                    session()->flash('message', 'প্রোফাইল তৈরি হয়েছে!');
                    $this->showModal = false;
                } else {
                    session()->flash('error', 'প্রোফাইল তৈরি করতে ব্যর্থ!');
                }

                $api->disconnect();
            }
        } catch (\Exception $e) {
            session()->flash('error', 'ত্রুটি: ' . $e->getMessage());
        }

        $this->loadData();
    }

    public function deleteProfile($id)
    {
        $router = Router::find($this->selectedRouter);
        if (!$router) return;

        try {
            $api = $router->getApi();
            
            if ($api->connect()) {
                $response = $api->command('/ip/hotspot/user/profile/remove', ['=.id=' . $id]);

                if (isset($response[0]) && $response[0] === '!done') {
                    session()->flash('message', 'প্রোফাইল ডিলিট হয়েছে!');
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

    public function render()
    {
        return view('livewire.profile-sync');
    }
}
