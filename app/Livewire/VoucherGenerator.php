<?php

namespace App\Livewire;

use App\Models\Router;
use App\Models\Package;
use App\Models\Voucher;
use Livewire\Component;
use Livewire\WithPagination;

class VoucherGenerator extends Component
{
    use WithPagination;

    public $routers = [];
    public $packages = [];
    
    // Form fields
    public $selectedRouter = '';
    public $selectedPackage = '';
    public $quantity = 10;
    public $prefix = '';
    public $usernameLength = 8;
    public $passwordLength = 8;
    public $userMode = 'voucher'; // voucher or user_pass
    public $characters = 'alphanumeric'; // alphanumeric, numbers, uppercase, lowercase

    // Generated vouchers
    public $generatedVouchers = [];
    public $showGenerated = false;

    public function mount()
    {
        $this->routers = Router::where('is_active', true)->get();
    }

    public function updatedSelectedRouter()
    {
        $this->packages = Package::where('router_id', $this->selectedRouter)
            ->where('is_active', true)
            ->get();
        $this->selectedPackage = '';
    }

    public function generate()
    {
        $this->validate([
            'selectedRouter' => 'required|exists:routers,id',
            'selectedPackage' => 'required|exists:packages,id',
            'quantity' => 'required|integer|min:1|max:100',
            'usernameLength' => 'required|integer|min:4|max:16',
        ]);

        // ভাউচার জেনারেট করুন
        $this->generatedVouchers = Voucher::generate(
            $this->selectedRouter,
            $this->selectedPackage,
            auth()->id(),
            $this->quantity,
            $this->prefix,
            $this->usernameLength,
            $this->passwordLength,
            $this->userMode
        );

        // MikroTik এ সিঙ্ক করুন
        $synced = 0;
        foreach ($this->generatedVouchers as $voucher) {
            if ($voucher->syncToMikrotik()) {
                $synced++;
            }
        }

        $this->showGenerated = true;
        
        session()->flash('message', "{$this->quantity} টি ভাউচার তৈরি হয়েছে! {$synced} টি MikroTik এ সিঙ্ক হয়েছে।");
    }

    public function resetForm()
    {
        $this->showGenerated = false;
        $this->generatedVouchers = [];
        $this->quantity = 10;
        $this->prefix = '';
    }

    public function render()
    {
        return view('livewire.voucher-generator');
    }
}
