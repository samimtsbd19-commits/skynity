<?php

namespace App\Livewire;

use App\Models\Router;
use App\Models\Package;
use Livewire\Component;
use Livewire\WithPagination;

class PackageManager extends Component
{
    use WithPagination;

    public $showModal = false;
    public $editMode = false;
    public $packageId = null;
    public $selectedRouter = '';

    public $routers = [];

    // Form fields
    public $router_id = '';
    public $name = '';
    public $mikrotik_profile = '';
    public $validity = '';
    public $data_limit = '';
    public $speed_limit = '';
    public $price = 0;
    public $selling_price = 0;
    public $description = '';

    protected $rules = [
        'router_id' => 'required|exists:routers,id',
        'name' => 'required|string|max:255',
        'mikrotik_profile' => 'required|string|max:255',
        'validity' => 'nullable|string|max:50',
        'data_limit' => 'nullable|string|max:50',
        'speed_limit' => 'nullable|string|max:50',
        'price' => 'required|numeric|min:0',
        'selling_price' => 'required|numeric|min:0',
        'description' => 'nullable|string',
    ];

    public function mount()
    {
        $this->routers = Router::where('is_active', true)->get();
    }

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
        $this->packageId = null;
        $this->router_id = '';
        $this->name = '';
        $this->mikrotik_profile = '';
        $this->validity = '';
        $this->data_limit = '';
        $this->speed_limit = '';
        $this->price = 0;
        $this->selling_price = 0;
        $this->description = '';
    }

    public function edit($id)
    {
        $package = Package::findOrFail($id);
        
        $this->editMode = true;
        $this->packageId = $package->id;
        $this->router_id = $package->router_id;
        $this->name = $package->name;
        $this->mikrotik_profile = $package->mikrotik_profile;
        $this->validity = $package->validity;
        $this->data_limit = $package->data_limit;
        $this->speed_limit = $package->speed_limit;
        $this->price = $package->price;
        $this->selling_price = $package->selling_price;
        $this->description = $package->description;
        
        $this->showModal = true;
    }

    public function editPackage($id)
    {
        $this->edit($id);
    }

    public function createPackage()
    {
        $this->openModal();
    }

    public function save()
    {
        $this->validate();

        $data = [
            'router_id' => $this->router_id,
            'name' => $this->name,
            'mikrotik_profile' => $this->mikrotik_profile,
            'validity' => $this->validity,
            'data_limit' => $this->data_limit,
            'speed_limit' => $this->speed_limit,
            'price' => $this->price,
            'selling_price' => $this->selling_price,
            'description' => $this->description,
        ];

        if ($this->editMode) {
            Package::find($this->packageId)->update($data);
            session()->flash('message', 'প্যাকেজ আপডেট হয়েছে!');
        } else {
            Package::create($data);
            session()->flash('message', 'প্যাকেজ যোগ হয়েছে!');
        }

        $this->closeModal();
    }

    public function toggleActive($id)
    {
        $package = Package::find($id);
        $package->update(['is_active' => !$package->is_active]);
    }

    public function delete($id)
    {
        Package::find($id)->delete();
        session()->flash('message', 'প্যাকেজ ডিলিট হয়েছে!');
    }

    public function deletePackage($id)
    {
        $this->delete($id);
    }

    public function render()
    {
        $query = Package::with('router');
        
        if ($this->selectedRouter) {
            $query->where('router_id', $this->selectedRouter);
        }
        
        return view('livewire.package-manager', [
            'packages' => $query->latest()->paginate(10),
        ]);
    }
}
