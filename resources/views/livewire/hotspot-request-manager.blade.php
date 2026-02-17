<div>
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 space-y-4 md:space-y-0">
        <div>
            <h1 class="text-2xl font-bold bg-gradient-to-r from-purple-400 to-pink-500 bg-clip-text text-transparent">হটস্পট রিকোয়েস্ট</h1>
            @if($this->pendingCount > 0)
            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-500/20 text-yellow-400 mt-2 border border-yellow-500/30">
                <span class="w-2 h-2 bg-yellow-500 rounded-full mr-2 animate-pulse"></span>
                {{ $this->pendingCount }} টি পেন্ডিং রিকোয়েস্ট
            </span>
            @endif
        </div>
        <div class="flex flex-wrap items-center gap-3">
            <select wire:model.live="filterStatus" class="border border-gray-700 rounded-lg px-3 py-2 bg-gray-800 text-gray-200 focus:ring-purple-500 focus:border-purple-500">
                <option value="">সব স্ট্যাটাস</option>
                <option value="pending">পেন্ডিং</option>
                <option value="approved">অনুমোদিত</option>
                <option value="rejected">বাতিল</option>
            </select>
            <select wire:model.live="filterRouter" class="border border-gray-700 rounded-lg px-3 py-2 bg-gray-800 text-gray-200 focus:ring-purple-500 focus:border-purple-500">
                <option value="">সব রাউটার</option>
                @foreach($routers as $router)
                <option value="{{ $router->id }}">{{ $router->name }}</option>
                @endforeach
            </select>
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="সার্চ করুন..." 
                   class="border border-gray-700 rounded-lg px-4 py-2 bg-gray-800 text-gray-200 placeholder-gray-500 focus:ring-purple-500 focus:border-purple-500">
        </div>
    </div>

    <!-- Flash Messages -->
    @if (session()->has('success'))
    <div class="bg-green-500/20 border border-green-500/30 text-green-400 px-4 py-3 rounded-lg mb-6">
        {{ session('success') }}
    </div>
    @endif
    @if (session()->has('error'))
    <div class="bg-red-500/20 border border-red-500/30 text-red-400 px-4 py-3 rounded-lg mb-6">
        {{ session('error') }}
    </div>
    @endif

    <!-- Requests Table -->
    <div class="bg-gray-800/50 backdrop-blur-sm rounded-xl border border-gray-700/50 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-900/50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase">গ্রাহক</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase">প্যাকেজ</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase">পেমেন্ট</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase">স্ট্যাটাস</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase">সময়</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-400 uppercase">অ্যাকশন</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700/50">
                    @forelse($requests as $request)
                    <tr class="hover:bg-gray-700/30 {{ $request->status === 'pending' ? 'bg-yellow-500/5' : '' }}">
                        <td class="px-4 py-4">
                            <div>
                                <p class="font-medium text-gray-200">{{ $request->customer_name }}</p>
                                <p class="text-sm text-gray-400">{{ $request->customer_phone }}</p>
                                @if($request->mac_address)
                                <p class="text-xs text-gray-500 font-mono">{{ $request->mac_address }}</p>
                                @endif
                            </div>
                        </td>
                        <td class="px-4 py-4">
                            <p class="font-medium text-gray-200">{{ $request->package->name ?? 'N/A' }}</p>
                            <p class="text-sm text-gray-400">{{ $request->router->name ?? 'N/A' }}</p>
                        </td>
                        <td class="px-4 py-4">
                            <p class="font-medium text-emerald-400">৳{{ number_format($request->amount) }}</p>
                            <p class="text-sm text-gray-400">{{ strtoupper($request->payment_method) }}</p>
                            @if($request->transaction_id)
                            <p class="text-xs text-cyan-400 font-mono">{{ $request->transaction_id }}</p>
                            @endif
                        </td>
                        <td class="px-4 py-4">
                            @if($request->status === 'pending')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-500/20 text-yellow-400 border border-yellow-500/30">
                                <span class="w-2 h-2 bg-yellow-500 rounded-full mr-1.5 animate-pulse"></span>
                                পেন্ডিং
                            </span>
                            @elseif($request->status === 'approved')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">
                                ✓ অনুমোদিত
                            </span>
                            @elseif($request->status === 'rejected')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-500/20 text-red-400 border border-red-500/30">
                                ✗ বাতিল
                            </span>
                            @endif
                        </td>
                        <td class="px-4 py-4 text-sm text-gray-400">
                            {{ $request->created_at->diffForHumans() }}
                        </td>
                        <td class="px-4 py-4 text-right">
                            @if($request->status === 'pending')
                            <div class="flex items-center justify-end space-x-2">
                                <button wire:click="openApproveModal({{ $request->id }})" 
                                        class="bg-gradient-to-r from-emerald-600 to-green-600 hover:from-emerald-500 hover:to-green-500 text-white px-3 py-1.5 rounded-lg text-sm font-medium transition">
                                    অনুমোদন
                                </button>
                                <button wire:click="openRejectModal({{ $request->id }})" 
                                        class="bg-gradient-to-r from-red-600 to-rose-600 hover:from-red-500 hover:to-rose-500 text-white px-3 py-1.5 rounded-lg text-sm font-medium transition">
                                    বাতিল
                                </button>
                            </div>
                            @else
                            <button wire:click="viewRequest({{ $request->id }})" 
                                    class="text-purple-400 hover:text-purple-300 text-sm font-medium">
                                বিস্তারিত
                            </button>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-12 text-center text-gray-500">
                            কোন রিকোয়েস্ট পাওয়া যায়নি
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 bg-gray-900/30 border-t border-gray-700/50">
            {{ $requests->links() }}
        </div>
    </div>

    <!-- Approve Modal -->
    @if($showApproveModal && $selectedRequest)
    <div class="fixed inset-0 z-50 overflow-y-auto" x-data x-init="document.body.classList.add('overflow-hidden')" x-on:close-modal.window="document.body.classList.remove('overflow-hidden')">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-black/70 backdrop-blur-sm" wire:click="$set('showApproveModal', false)"></div>
            <div class="relative bg-gradient-to-br from-gray-800 to-gray-900 rounded-xl shadow-2xl max-w-md w-full p-6 border border-gray-700/50">
                <h3 class="text-lg font-bold bg-gradient-to-r from-emerald-400 to-green-400 bg-clip-text text-transparent mb-4">রিকোয়েস্ট অনুমোদন করুন</h3>
                
                <div class="bg-gray-700/30 rounded-lg p-4 mb-6 border border-gray-600/30">
                    <div class="space-y-2 text-sm">
                        <p><span class="text-gray-400">গ্রাহক:</span> <span class="font-medium text-gray-200">{{ $selectedRequest->customer_name }}</span></p>
                        <p><span class="text-gray-400">ফোন:</span> <span class="font-medium text-gray-200">{{ $selectedRequest->customer_phone }}</span></p>
                        <p><span class="text-gray-400">প্যাকেজ:</span> <span class="font-medium text-purple-400">{{ $selectedRequest->package->name ?? 'N/A' }}</span></p>
                        <p><span class="text-gray-400">মূল্য:</span> <span class="font-medium text-emerald-400">৳{{ number_format($selectedRequest->amount) }}</span></p>
                        <p><span class="text-gray-400">Transaction:</span> <span class="font-medium font-mono text-cyan-400">{{ $selectedRequest->transaction_id }}</span></p>
                    </div>
                </div>

                <!-- Stock User Toggle -->
                <div class="bg-cyan-500/10 border border-cyan-500/30 rounded-lg p-4 mb-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-cyan-400 font-medium">স্টক ইউজার থেকে অ্যাসাইন</p>
                            <p class="text-xs text-gray-400 mt-1">
                                Available: <span class="text-cyan-400 font-bold">{{ $this->availableStockCount }}</span> জন
                            </p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" wire:model="useStockUser" class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-600 rounded-full peer peer-checked:bg-cyan-600 peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all"></div>
                        </label>
                    </div>
                </div>

                <div class="bg-emerald-500/10 border border-emerald-500/30 rounded-lg p-4 mb-6">
                    <p class="text-sm text-emerald-400">
                        <strong>অনুমোদন করলে যা হবে:</strong><br>
                        @if($useStockUser && $this->availableStockCount > 0)
                        ✓ স্টক থেকে ইউজার অ্যাসাইন হবে<br>
                        ✓ MikroTik এ ইউজার Enable হবে<br>
                        @else
                        ✓ নতুন ইউজার অ্যাকাউন্ট তৈরি হবে<br>
                        ✓ MikroTik এ হটস্পট ইউজার তৈরি হবে<br>
                        @endif
                        ✓ গ্রাহক নোটিফিকেশন পাবে<br>
                        ✓ ভাউচার রেকর্ড তৈরি হবে
                    </p>
                </div>

                <div class="flex justify-end space-x-3">
                    <button wire:click="$set('showApproveModal', false)" class="px-4 py-2 bg-gray-700 text-gray-300 rounded-lg hover:bg-gray-600 transition border border-gray-600">
                        বাতিল
                    </button>
                    <button wire:click="approveRequest" class="px-4 py-2 bg-gradient-to-r from-emerald-600 to-green-600 text-white rounded-lg hover:from-emerald-500 hover:to-green-500 transition">
                        অনুমোদন করুন
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Reject Modal -->
    @if($showRejectModal && $selectedRequest)
    <div class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-black/70 backdrop-blur-sm" wire:click="$set('showRejectModal', false)"></div>
            <div class="relative bg-gradient-to-br from-gray-800 to-gray-900 rounded-xl shadow-2xl max-w-md w-full p-6 border border-gray-700/50">
                <h3 class="text-lg font-bold bg-gradient-to-r from-red-400 to-rose-400 bg-clip-text text-transparent mb-4">রিকোয়েস্ট বাতিল করুন</h3>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-300 mb-2">বাতিলের কারণ</label>
                    <textarea wire:model="rejectionReason" rows="3" 
                              class="w-full border border-gray-600 rounded-lg px-3 py-2 bg-gray-700/50 text-gray-200 focus:ring-2 focus:ring-red-500 focus:border-red-500 placeholder-gray-500"
                              placeholder="কারণ লিখুন (ঐচ্ছিক)"></textarea>
                </div>

                <div class="flex justify-end space-x-3">
                    <button wire:click="$set('showRejectModal', false)" class="px-4 py-2 bg-gray-700 text-gray-300 rounded-lg hover:bg-gray-600 transition border border-gray-600">
                        বাতিল
                    </button>
                    <button wire:click="rejectRequest" class="px-4 py-2 bg-gradient-to-r from-red-600 to-rose-600 text-white rounded-lg hover:from-red-500 hover:to-rose-500 transition">
                        রিকোয়েস্ট বাতিল করুন
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
