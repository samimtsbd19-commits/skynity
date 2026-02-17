<?php

namespace App\Livewire;

use App\Models\Router;
use App\Models\Package;
use App\Models\Voucher;
use Livewire\Component;
use Livewire\WithPagination;

class VoucherList extends Component
{
    use WithPagination;

    public $routers = [];
    public $packages = [];
    
    // Filters
    public $filterRouter = '';
    public $filterPackage = '';
    public $filterStatus = '';
    public $filterDate = '';
    public $search = '';

    // Bulk actions
    public $selected = [];
    public $selectAll = false;

    public function mount()
    {
        $this->routers = Router::where('is_active', true)->get();
    }

    public function updatedFilterRouter()
    {
        $this->packages = Package::where('router_id', $this->filterRouter)->get();
        $this->filterPackage = '';
        $this->resetPage();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selected = $this->getVouchersQuery()->pluck('id')->toArray();
        } else {
            $this->selected = [];
        }
    }

    public function deleteSelected()
    {
        if (empty($this->selected)) return;

        Voucher::whereIn('id', $this->selected)->delete();
        
        $this->selected = [];
        $this->selectAll = false;
        
        session()->flash('message', 'নির্বাচিত ভাউচারগুলো ডিলিট হয়েছে!');
    }

    public function printSelected()
    {
        if (empty($this->selected)) return;

        return redirect()->route('vouchers.print', [
            'ids' => implode(',', $this->selected)
        ]);
    }

    public function syncSelected()
    {
        if (empty($this->selected)) return;

        $vouchers = Voucher::whereIn('id', $this->selected)->get();
        $synced = 0;

        foreach ($vouchers as $voucher) {
            if ($voucher->syncToMikrotik()) {
                $synced++;
            }
        }

        session()->flash('message', "{$synced} টি ভাউচার MikroTik এ সিঙ্ক হয়েছে!");
    }

    public function delete($id)
    {
        Voucher::find($id)->delete();
        session()->flash('message', 'ভাউচার ডিলিট হয়েছে!');
    }

    public function toggleStatus($id)
    {
        $voucher = Voucher::find($id);
        $newStatus = $voucher->status === 'disabled' ? 'unused' : 'disabled';
        $voucher->update(['status' => $newStatus]);
    }

    private function getVouchersQuery()
    {
        return Voucher::with(['router', 'package', 'creator'])
            ->when($this->filterRouter, fn($q) => $q->where('router_id', $this->filterRouter))
            ->when($this->filterPackage, fn($q) => $q->where('package_id', $this->filterPackage))
            ->when($this->filterStatus, fn($q) => $q->where('status', $this->filterStatus))
            ->when($this->filterDate, fn($q) => $q->whereDate('created_at', $this->filterDate))
            ->when($this->search, fn($q) => $q->where('username', 'like', "%{$this->search}%"))
            ->latest();
    }

    public function render()
    {
        return view('livewire.voucher-list', [
            'vouchers' => $this->getVouchersQuery()->paginate(20),
        ]);
    }
}
