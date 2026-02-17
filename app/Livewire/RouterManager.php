<?php

namespace App\Livewire;

use App\Models\Router;
use Livewire\Component;
use Livewire\WithPagination;

class RouterManager extends Component
{
    use WithPagination;

    public $showModal = false;
    public $editMode = false;
    public $routerId = null;

    // Form fields
    public $name = '';
    public $ip_address = '';
    public $port = 8728;
    public $username = '';
    public $password = '';
    public $hotspot_name = '';
    public $dns_name = '';
    public $hotspot_url = '';

    // Connection test result
    public $testResult = null;

    protected $rules = [
        'name' => 'required|string|max:255',
        'ip_address' => 'required|ip',
        'port' => 'required|integer|min:1|max:65535',
        'username' => 'required|string|max:255',
        'password' => 'required|string|max:255',
        'hotspot_name' => 'nullable|string|max:255',
        'dns_name' => 'nullable|string|max:255',
        'hotspot_url' => 'nullable|url|max:255',
    ];

    public function openModal()
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->editMode = false;
        $this->routerId = null;
        $this->name = '';
        $this->ip_address = '';
        $this->port = 8728;
        $this->username = '';
        $this->password = '';
        $this->hotspot_name = '';
        $this->dns_name = '';
        $this->hotspot_url = '';
        $this->testResult = null;
    }

    public function edit($id)
    {
        $router = Router::findOrFail($id);
        
        $this->editMode = true;
        $this->routerId = $router->id;
        $this->name = $router->name;
        $this->ip_address = $router->ip_address;
        $this->port = $router->port;
        $this->username = $router->username;
        $this->password = ''; // Don't show encrypted password
        $this->hotspot_name = $router->hotspot_name;
        $this->dns_name = $router->dns_name;
        $this->hotspot_url = $router->hotspot_url;
        
        $this->showModal = true;
    }

    public function save()
    {
        // Password not required when editing
        if ($this->editMode) {
            $rules = $this->rules;
            $rules['password'] = 'nullable|string|max:255';
            $this->validate($rules);
        } else {
            $this->validate();
        }

        $data = [
            'name' => $this->name,
            'ip_address' => $this->ip_address,
            'port' => $this->port,
            'username' => $this->username,
            'hotspot_name' => $this->hotspot_name,
            'dns_name' => $this->dns_name,
            'hotspot_url' => $this->hotspot_url,
        ];

        if ($this->password) {
            $data['password'] = $this->password;
        }

        if ($this->editMode) {
            Router::find($this->routerId)->update($data);
            session()->flash('message', 'রাউটার আপডেট হয়েছে!');
        } else {
            Router::create($data);
            session()->flash('message', 'রাউটার যোগ হয়েছে!');
        }

        $this->closeModal();
    }

    public function testConnection($id = null)
    {
        if ($id) {
            $router = Router::find($id);
            $connected = $router->testConnection();
        } else {
            // Test from form
            $router = new Router([
                'ip_address' => $this->ip_address,
                'port' => $this->port,
                'username' => $this->username,
            ]);
            $router->password = $this->password;
            
            $api = $router->getApi();
            $connected = $api->connect();
            if ($connected) $api->disconnect();
        }

        $this->testResult = $connected ? 'success' : 'failed';
        
        if ($connected) {
            session()->flash('message', 'কানেকশন সফল!');
        } else {
            session()->flash('error', 'কানেকশন ব্যর্থ! IP, Port, Username, Password চেক করুন।');
        }
    }

    public function toggleActive($id)
    {
        $router = Router::find($id);
        $router->update(['is_active' => !$router->is_active]);
    }

    public function delete($id)
    {
        Router::find($id)->delete();
        session()->flash('message', 'রাউটার ডিলিট হয়েছে!');
    }

    public function render()
    {
        return view('livewire.router-manager', [
            'routers' => Router::latest()->paginate(10),
        ]);
    }
}
