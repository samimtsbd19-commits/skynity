<div>
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-xl font-bold text-white">ভাউচার তালিকা</h1>
            <p class="text-gray-500 text-sm mt-0.5">সব ভাউচার পরিচালনা করুন</p>
        </div>
        <a href="{{ route('vouchers.generate') }}"
           class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-500 hover:to-pink-500 text-white text-sm font-semibold rounded-xl transition shadow-lg shadow-purple-500/20">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            নতুন ভাউচার
        </a>
    </div>

    <!-- Filters -->
    <div class="bg-gray-800/60 backdrop-blur rounded-2xl border border-gray-700/60 p-4 mb-5">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="ইউজারনেম খুঁজুন..."
                   class="w-full px-4 py-2.5 bg-gray-900/60 border border-gray-700 rounded-xl text-white text-sm placeholder-gray-500 focus:outline-none focus:border-purple-500 focus:ring-1 focus:ring-purple-500/30 transition sm:col-span-2 lg:col-span-1">
            <select wire:model.live="filterRouter"
                    class="w-full px-4 py-2.5 bg-gray-900/60 border border-gray-700 rounded-xl text-gray-300 text-sm focus:outline-none focus:border-purple-500 transition">
                <option value="">সব রাউটার</option>
                @foreach($routers as $router)
                <option value="{{ $router->id }}">{{ $router->name }}</option>
                @endforeach
            </select>
            <select wire:model.live="filterPackage"
                    class="w-full px-4 py-2.5 bg-gray-900/60 border border-gray-700 rounded-xl text-gray-300 text-sm focus:outline-none focus:border-purple-500 transition">
                <option value="">সব প্যাকেজ</option>
                @foreach($packages as $package)
                <option value="{{ $package->id }}">{{ $package->name }}</option>
                @endforeach
            </select>
            <select wire:model.live="filterStatus"
                    class="w-full px-4 py-2.5 bg-gray-900/60 border border-gray-700 rounded-xl text-gray-300 text-sm focus:outline-none focus:border-purple-500 transition">
                <option value="">সব স্ট্যাটাস</option>
                <option value="unused">অব্যবহৃত</option>
                <option value="active">সক্রিয়</option>
                <option value="expired">মেয়াদোত্তীর্ণ</option>
            </select>
            <input type="date" wire:model.live="filterDate"
                   class="w-full px-4 py-2.5 bg-gray-900/60 border border-gray-700 rounded-xl text-gray-300 text-sm focus:outline-none focus:border-purple-500 transition">
        </div>
    </div>

    <!-- Bulk Actions -->
    @if(count($selected) > 0)
    <div class="bg-purple-500/10 border border-purple-500/30 rounded-2xl p-4 mb-5 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
        <span class="text-purple-300 font-semibold text-sm">{{ count($selected) }}টি ভাউচার নির্বাচিত</span>
        <div class="flex items-center gap-2">
            <a href="{{ route('vouchers.print', ['ids' => implode(',', $selected)]) }}" target="_blank"
               class="inline-flex items-center gap-2 px-4 py-2 bg-gray-700 text-gray-300 text-sm font-medium rounded-xl hover:bg-gray-600 transition border border-gray-600">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                প্রিন্ট
            </a>
            <button wire:click="deleteSelected" wire:confirm="নির্বাচিত ভাউচারগুলো মুছে ফেলতে চান?"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-xl hover:bg-red-700 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
                মুছুন
            </button>
        </div>
    </div>
    @endif

    <!-- ========================
         DESKTOP TABLE
    ======================== -->
    <div class="hidden sm:block bg-gray-800/60 backdrop-blur rounded-2xl border border-gray-700/60 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-900/60">
                    <tr class="text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">
                        <th class="px-4 py-4">
                            <input type="checkbox" wire:model.live="selectAll" class="rounded border-gray-600 bg-gray-700 text-purple-600 focus:ring-purple-500">
                        </th>
                        <th class="px-4 py-4">ইউজারনেম</th>
                        <th class="px-4 py-4">পাসওয়ার্ড</th>
                        <th class="px-4 py-4">প্যাকেজ</th>
                        <th class="px-4 py-4">রাউটার</th>
                        <th class="px-4 py-4">স্ট্যাটাস</th>
                        <th class="px-4 py-4">তারিখ</th>
                        <th class="px-4 py-4 text-right">অ্যাকশন</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700/40">
                    @forelse($vouchers as $voucher)
                    <tr class="hover:bg-gray-700/30 transition-colors">
                        <td class="px-4 py-3">
                            <input type="checkbox" wire:model.live="selected" value="{{ $voucher->id }}"
                                   class="rounded border-gray-600 bg-gray-700 text-purple-600 focus:ring-purple-500">
                        </td>
                        <td class="px-4 py-3">
                            <span class="font-mono text-cyan-400 font-bold text-sm">{{ $voucher->username }}</span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="font-mono text-gray-400 text-sm">{{ $voucher->password }}</span>
                        </td>
                        <td class="px-4 py-3 text-gray-400 text-sm">{{ $voucher->package->name ?? '—' }}</td>
                        <td class="px-4 py-3 text-gray-500 text-sm">{{ $voucher->router->name ?? '—' }}</td>
                        <td class="px-4 py-3">
                            @if($voucher->status === 'unused')
                            <span class="px-2.5 py-1 bg-emerald-500/15 text-emerald-400 text-xs font-bold rounded-full border border-emerald-500/30">অব্যবহৃত</span>
                            @elseif($voucher->status === 'active')
                            <span class="px-2.5 py-1 bg-blue-500/15 text-blue-400 text-xs font-bold rounded-full border border-blue-500/30">সক্রিয়</span>
                            @else
                            <span class="px-2.5 py-1 bg-red-500/15 text-red-400 text-xs font-bold rounded-full border border-red-500/30">মেয়াদোত্তীর্ণ</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-gray-500 text-xs">{{ $voucher->created_at->format('d M Y') }}</td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-end gap-1">
                                <a href="{{ route('vouchers.print', ['ids' => $voucher->id]) }}" target="_blank"
                                   class="p-2 text-gray-400 hover:text-white hover:bg-gray-700 rounded-lg transition" title="প্রিন্ট">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                                    </svg>
                                </a>
                                <button wire:click="deleteVoucher({{ $voucher->id }})" wire:confirm="এই ভাউচার মুছে ফেলতে চান?"
                                        class="p-2 text-red-400 hover:bg-red-500/15 rounded-lg transition" title="ডিলিট">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-4 py-16 text-center">
                            <div class="w-14 h-14 mx-auto mb-4 rounded-2xl bg-gray-700/50 flex items-center justify-center">
                                <svg class="w-7 h-7 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
                                </svg>
                            </div>
                            <p class="text-gray-500 font-medium">কোনো ভাউচার পাওয়া যায়নি</p>
                            <a href="{{ route('vouchers.generate') }}" class="mt-3 inline-block text-purple-400 hover:text-purple-300 text-sm">নতুন ভাউচার তৈরি করুন →</a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($vouchers->hasPages())
        <div class="px-5 py-4 border-t border-gray-700/40">{{ $vouchers->links() }}</div>
        @endif
    </div>

    <!-- ========================
         MOBILE CARDS
    ======================== -->
    <div class="sm:hidden space-y-3">
        @forelse($vouchers as $voucher)
        <div class="bg-gray-800/60 backdrop-blur rounded-2xl border border-gray-700/60 p-4">
            <div class="flex items-start justify-between mb-3">
                <div class="flex items-center gap-2">
                    <input type="checkbox" wire:model.live="selected" value="{{ $voucher->id }}"
                           class="rounded border-gray-600 bg-gray-700 text-purple-600 focus:ring-purple-500 mt-0.5">
                    <div>
                        <p class="font-mono font-bold text-cyan-400">{{ $voucher->username }}</p>
                        <p class="font-mono text-gray-500 text-xs">{{ $voucher->password }}</p>
                    </div>
                </div>
                @if($voucher->status === 'unused')
                <span class="px-2.5 py-1 bg-emerald-500/15 text-emerald-400 text-xs font-bold rounded-full border border-emerald-500/30">অব্যবহৃত</span>
                @elseif($voucher->status === 'active')
                <span class="px-2.5 py-1 bg-blue-500/15 text-blue-400 text-xs font-bold rounded-full border border-blue-500/30">সক্রিয়</span>
                @else
                <span class="px-2.5 py-1 bg-red-500/15 text-red-400 text-xs font-bold rounded-full border border-red-500/30">মেয়াদোত্তীর্ণ</span>
                @endif
            </div>
            <div class="flex gap-3 text-xs text-gray-500 mb-3 pb-3 border-b border-gray-700/40">
                <span>{{ $voucher->package->name ?? '—' }}</span>
                <span>•</span>
                <span>{{ $voucher->router->name ?? '—' }}</span>
                <span>•</span>
                <span>{{ $voucher->created_at->format('d M Y') }}</span>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('vouchers.print', ['ids' => $voucher->id]) }}" target="_blank"
                   class="flex-1 py-2 text-xs font-semibold bg-gray-700 text-gray-300 border border-gray-600 rounded-xl hover:bg-gray-600 transition flex items-center justify-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                    </svg>
                    প্রিন্ট
                </a>
                <button wire:click="deleteVoucher({{ $voucher->id }})" wire:confirm="এই ভাউচার মুছে ফেলতে চান?"
                        class="flex-1 py-2 text-xs font-semibold bg-red-500/10 text-red-400 border border-red-500/30 rounded-xl hover:bg-red-500/20 transition flex items-center justify-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    মুছুন
                </button>
            </div>
        </div>
        @empty
        <div class="bg-gray-800/60 rounded-2xl border border-gray-700/60 p-12 text-center">
            <div class="w-14 h-14 mx-auto mb-4 rounded-2xl bg-gray-700/50 flex items-center justify-center">
                <svg class="w-7 h-7 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
                </svg>
            </div>
            <p class="text-gray-500 font-medium">কোনো ভাউচার নেই</p>
            <a href="{{ route('vouchers.generate') }}" class="mt-3 inline-block text-purple-400 text-sm">নতুন তৈরি করুন →</a>
        </div>
        @endforelse

        @if($vouchers->hasPages())
        <div class="py-2">{{ $vouchers->links() }}</div>
        @endif
    </div>
</div>
