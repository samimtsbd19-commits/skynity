<?php

namespace App\Livewire;

use App\Models\HotspotRequest;
use App\Models\Notification;
use App\Models\StockUser;
use App\Models\User;
use App\Models\Voucher;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithPagination;

class HotspotRequestManager extends Component
{
    use WithPagination;

    public $search = '';
    public $filterStatus = '';
    public $filterRouter = '';
    public $selectedRequest = null;
    public $showApproveModal = false;
    public $showRejectModal = false;
    public $rejectionReason = '';
    public $useStockUser = true; // ডিফল্ট: স্টক থেকে ইউজার নেওয়া

    protected $queryString = ['search', 'filterStatus', 'filterRouter'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function viewRequest($id)
    {
        $this->selectedRequest = HotspotRequest::with(['router', 'package', 'user'])->find($id);
    }

    public function openApproveModal($id)
    {
        $this->selectedRequest = HotspotRequest::with(['router', 'package'])->find($id);
        $this->showApproveModal = true;
    }

    public function openRejectModal($id)
    {
        $this->selectedRequest = HotspotRequest::find($id);
        $this->showRejectModal = true;
        $this->rejectionReason = '';
    }

    public function approveRequest()
    {
        if (!$this->selectedRequest) return;

        $request = $this->selectedRequest;
        $package = $request->package;
        $router = $request->router;

        try {
            $username = null;
            $password = null;
            $stockUser = null;
            $createdFromStock = false;

            // প্রথমে স্টক ইউজার থেকে অ্যাসাইন করার চেষ্টা করি
            if ($this->useStockUser) {
                $stockUser = StockUser::assignToRequest($router->id, $request->id, $package->id);
                
                if ($stockUser) {
                    $username = $stockUser->username;
                    $password = $stockUser->password;
                    $createdFromStock = true;

                    // MikroTik এ ইউজার Enable করি
                    $api = $router->getApi();
                    if ($api->connect()) {
                        try {
                            // ইউজার Enable করি
                            $api->enableHotspotUser($username);
                            
                            // MAC Binding আপডেট করি
                            if ($request->mac_address) {
                                $api->updateHotspotUserMac($username, $request->mac_address);
                            }
                        } catch (\Exception $e) {
                            // API এরর হলে স্কিপ করি, ইউজার তৈরি আছে
                        }
                        $api->disconnect();
                    }
                }
            }

            // স্টক থেকে না পেলে লাইভ ইউজার তৈরি করি
            if (!$stockUser) {
                $username = 'user' . Str::random(6);
                $password = Str::random(8);

                $api = $router->getApi();
                if ($api->connect()) {
                    $api->addHotspotUser(
                        $username,
                        $password,
                        $package->mikrotik_profile,
                        $request->mac_address
                    );
                    if ($request->mac_address) {
                        $api->updateHotspotUserMac($username, $request->mac_address);
                    }
                    $api->disconnect();
                }
            }

            // Create user account for customer
            $user = User::create([
                'name' => $request->customer_name,
                'email' => $request->customer_email ?? $request->customer_phone . '@hotspot.local',
                'phone' => $request->customer_phone,
                'password' => Hash::make($password),
                'role' => 'customer',
                'hotspot_username' => $username,
                'hotspot_password' => $password,
                'router_id' => $router->id,
                'package_id' => $package->id,
                'subscription_expires_at' => null,
                'mac_address' => $request->mac_address,
                'is_active' => true,
            ]);

            // Create voucher record
            $voucher = Voucher::create([
                'router_id' => $router->id,
                'package_id' => $package->id,
                'username' => $username,
                'password' => $password,
                'status' => 'used',
                'used_at' => now(),
                'expires_at' => null,
                'created_by' => auth()->id(),
                'from_stock' => $createdFromStock,
            ]);

            // Update hotspot request
            $request->update([
                'status' => 'approved',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
                'user_id' => $user->id,
                'voucher_code' => $username,
                'from_stock_user' => $createdFromStock,
            ]);

            // Send notification to customer
            Notification::sendToUser(
                $user->id,
                'approval',
                'আপনার ইন্টারনেট অ্যাক্টিভ হয়েছে!',
                "ইউজারনেম: {$username}\nপাসওয়ার্ড: {$password}\nপ্যাকেজ: {$package->name}\nমেয়াদ: প্রথম কানেক্টের পর শুরু হবে",
                [
                    'username' => $username,
                    'password' => $password,
                    'package' => $package->name,
                    'expires_at' => null,
                ]
            );

            // Create sale record
            $request->package->sales()->create([
                'voucher_id' => $voucher->id,
                'sold_by' => auth()->id(),
                'amount' => $request->amount,
                'payment_method' => $request->payment_method,
            ]);

            $this->showApproveModal = false;
            $this->selectedRequest = null;
            
            $stockMsg = $createdFromStock ? ' (স্টক থেকে)' : ' (নতুন তৈরি)';
            session()->flash('success', 'রিকোয়েস্ট অনুমোদন করা হয়েছে। ইউজার: ' . $username . $stockMsg);

        } catch (\Exception $e) {
            session()->flash('error', 'অনুমোদন করতে সমস্যা হয়েছে: ' . $e->getMessage());
        }
    }

    public function getAvailableStockCountProperty()
    {
        if (!$this->selectedRequest) return 0;
        return StockUser::getAvailableCount($this->selectedRequest->router_id);
    }

    public function rejectRequest()
    {
        if (!$this->selectedRequest) return;

        $this->selectedRequest->update([
            'status' => 'rejected',
            'rejection_reason' => $this->rejectionReason,
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        $this->showRejectModal = false;
        $this->selectedRequest = null;
        $this->rejectionReason = '';
        session()->flash('success', 'রিকোয়েস্ট বাতিল করা হয়েছে।');
    }

    public function getPendingCountProperty()
    {
        return HotspotRequest::pending()->count();
    }

    public function render()
    {
        $requests = HotspotRequest::with(['router', 'package', 'user', 'approvedBy'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('customer_name', 'like', '%' . $this->search . '%')
                      ->orWhere('customer_phone', 'like', '%' . $this->search . '%')
                      ->orWhere('transaction_id', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->filterStatus, fn($q) => $q->where('status', $this->filterStatus))
            ->when($this->filterRouter, fn($q) => $q->where('router_id', $this->filterRouter))
            ->latest()
            ->paginate(15);

        $routers = \App\Models\Router::where('is_active', true)->get();

        return view('livewire.hotspot-request-manager', [
            'requests' => $requests,
            'routers' => $routers,
        ]);
    }
}
