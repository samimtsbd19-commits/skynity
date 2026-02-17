<div>
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 space-y-4 md:space-y-0">
        <h1 class="text-xl md:text-2xl font-bold bg-gradient-to-r from-purple-400 to-pink-500 bg-clip-text text-transparent">প্রোফাইল সিঙ্ক</h1>
        <div class="flex flex-wrap items-center gap-2">
            <button wire:click="syncFromMikrotik" class="bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white px-3 md:px-4 py-2 rounded-lg flex items-center space-x-2 transition text-sm">
                <svg class="w-4 h-4 md:w-5 md:h-5" wire:loading.class="animate-spin" wire:target="syncFromMikrotik" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                <span>MikroTik থেকে সিঙ্ক</span>
            </button>
            <button wire:click="openCreateModal" class="bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-500 hover:to-purple-500 text-white px-3 md:px-4 py-2 rounded-lg flex items-center space-x-2 transition text-sm">
                <svg class="w-4 h-4 md:w-5 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                <span>নতুন প্রোফাইল</span>
            </button>
        </div>
    </div>

    <!-- Flash Messages -->
    @if (session()->has('message'))
        <div class="bg-green-500/20 border border-green-500/30 text-green-400 px-4 py-3 rounded-lg mb-6">
            {{ session('message') }}
        </div>
    @endif
    @if (session()->has('error'))
        <div class="bg-red-500/20 border border-red-500/30 text-red-400 px-4 py-3 rounded-lg mb-6">
            {{ session('error') }}
        </div>
    @endif

    <!-- Router Select -->
    <div class="bg-gray-800/50 backdrop-blur-sm rounded-xl border border-gray-700/50 p-3 md:p-4 mb-6">
        <div class="flex flex-col md:flex-row items-start md:items-center gap-3">
            <label class="text-gray-400">রাউটার:</label>
            <select wire:model.live="selectedRouter" class="w-full md:w-64 border border-gray-600 rounded-lg px-4 py-2 bg-gray-700/50 text-gray-200 focus:ring-2 focus:ring-purple-500">
                @foreach($routers as $router)
                    <option value="{{ $router->id }}">{{ $router->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 md:gap-4 mb-6">
        <div class="bg-gradient-to-br from-indigo-600/20 to-purple-600/20 rounded-xl p-3 md:p-4 border border-indigo-500/30">
            <div class="flex items-center space-x-3">
                <div class="p-2 bg-indigo-500/20 rounded-lg">
                    <svg class="w-5 h-5 md:w-6 md:h-6 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-xl md:text-2xl font-bold text-indigo-400">{{ count($mikrotikProfiles) }}</p>
                    <p class="text-xs md:text-sm text-gray-400">MikroTik</p>
                </div>
            </div>
        </div>
        <div class="bg-gradient-to-br from-emerald-600/20 to-teal-600/20 rounded-xl p-3 md:p-4 border border-emerald-500/30">
            <div class="flex items-center space-x-3">
                <div class="p-2 bg-emerald-500/20 rounded-lg">
                    <svg class="w-5 h-5 md:w-6 md:h-6 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-xl md:text-2xl font-bold text-emerald-400">{{ count($localPackages) }}</p>
                    <p class="text-xs md:text-sm text-gray-400">লোকাল</p>
                </div>
            </div>
        </div>
        <div class="bg-gradient-to-br from-cyan-600/20 to-blue-600/20 rounded-xl p-3 md:p-4 border border-cyan-500/30">
            <div class="flex items-center space-x-3">
                <div class="p-2 bg-cyan-500/20 rounded-lg">
                    <svg class="w-5 h-5 md:w-6 md:h-6 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-xl md:text-2xl font-bold text-cyan-400">{{ $syncedCount ?? 0 }}</p>
                    <p class="text-xs md:text-sm text-gray-400">সিঙ্কড</p>
                </div>
            </div>
        </div>
        <div class="bg-gradient-to-br from-amber-600/20 to-orange-600/20 rounded-xl p-3 md:p-4 border border-amber-500/30">
            <div class="flex items-center space-x-3">
                <div class="p-2 bg-amber-500/20 rounded-lg">
                    <svg class="w-5 h-5 md:w-6 md:h-6 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-xl md:text-2xl font-bold text-amber-400">{{ $unsyncedCount ?? 0 }}</p>
                    <p class="text-xs md:text-sm text-gray-400">আনসিঙ্কড</p>
                </div>
            </div>
        </div>
    </div>

    @if($isLoading)
        <div class="bg-gray-800/50 rounded-xl border border-gray-700/50 p-12 text-center">
            <svg class="animate-spin h-8 w-8 text-purple-500 mx-auto" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <p class="mt-2 text-gray-400">লোড হচ্ছে...</p>
        </div>
    @else
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- MikroTik Profiles -->
        <div class="bg-gray-800/50 backdrop-blur-sm rounded-xl border border-gray-700/50 overflow-hidden">
            <div class="bg-gray-900/50 px-4 py-3 border-b border-gray-700/50">
                <h2 class="text-lg font-semibold text-indigo-400 flex items-center space-x-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01"></path>
                    </svg>
                    <span>MikroTik প্রোফাইল</span>
                </h2>
            </div>
            
            <!-- Mobile Cards -->
            <div class="md:hidden divide-y divide-gray-700/50">
                @forelse($mikrotikProfiles as $profile)
                <div class="p-4 hover:bg-gray-700/30">
                    <div class="flex justify-between items-start mb-2">
                        <div>
                            <p class="font-medium text-gray-200">{{ $profile['name'] }}</p>
                            <div class="flex flex-wrap gap-1 mt-1">
                                @if(isset($profile['rate_limit']) && $profile['rate_limit'])
                                    <span class="bg-cyan-500/20 text-cyan-400 px-2 py-0.5 rounded text-xs border border-cyan-500/30">{{ $profile['rate_limit'] }}</span>
                                @endif
                                @if(isset($profile['shared_users']) && $profile['shared_users'])
                                    <span class="bg-purple-500/20 text-purple-400 px-2 py-0.5 rounded text-xs border border-purple-500/30">{{ $profile['shared_users'] }} shared</span>
                                @endif
                            </div>
                        </div>
                        @if($profile['synced'] ?? false)
                            <span class="bg-emerald-500/20 text-emerald-400 px-2 py-1 rounded text-xs border border-emerald-500/30">✓</span>
                        @else
                            <button wire:click="syncProfile('{{ $profile['name'] }}', '{{ $profile['rate_limit'] ?? '' }}')" class="bg-indigo-500/20 text-indigo-400 hover:bg-indigo-500/30 px-2 py-1 rounded text-xs border border-indigo-500/30">Import</button>
                        @endif
                    </div>
                </div>
                @empty
                <div class="p-12 text-center text-gray-500">কোনো প্রোফাইল নেই</div>
                @endforelse
            </div>

            <!-- Desktop Table -->
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-900/30">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase">নাম</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase">স্পীড</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-400 uppercase">অ্যাকশন</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700/50">
                        @forelse($mikrotikProfiles as $profile)
                        <tr class="hover:bg-gray-700/30 transition-colors">
                            <td class="px-4 py-3">
                                <span class="font-medium text-gray-200">{{ $profile['name'] }}</span>
                            </td>
                            <td class="px-4 py-3 text-sm text-cyan-400">{{ $profile['rate_limit'] ?? 'N/A' }}</td>
                            <td class="px-4 py-3 text-right">
                                @if($profile['synced'] ?? false)
                                    <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">সিংকড ✓</span>
                                @else
                                    <button wire:click="syncProfile('{{ $profile['name'] }}', '{{ $profile['rate_limit'] ?? '' }}')" class="bg-indigo-500/20 text-indigo-400 hover:bg-indigo-500/30 px-3 py-1 rounded text-xs transition border border-indigo-500/30">Import</button>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="3" class="px-4 py-12 text-center text-gray-500">কোনো প্রোফাইল নেই</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Local Packages -->
        <div class="bg-gray-800/50 backdrop-blur-sm rounded-xl border border-gray-700/50 overflow-hidden">
            <div class="bg-gray-900/50 px-4 py-3 border-b border-gray-700/50">
                <h2 class="text-lg font-semibold text-emerald-400 flex items-center space-x-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path>
                    </svg>
                    <span>লোকাল প্যাকেজ</span>
                </h2>
            </div>

            <!-- Mobile Cards -->
            <div class="md:hidden divide-y divide-gray-700/50">
                @forelse($localPackages as $package)
                <div class="p-4 hover:bg-gray-700/30">
                    <div class="flex justify-between items-start mb-2">
                        <div>
                            <p class="font-medium text-gray-200">{{ $package['name'] ?? 'N/A' }}</p>
                            <div class="flex flex-wrap gap-1 mt-1">
                                <span class="bg-cyan-500/20 text-cyan-400 px-2 py-0.5 rounded text-xs border border-cyan-500/30">৳{{ $package['price'] ?? 0 }}</span>
                                <span class="bg-purple-500/20 text-purple-400 px-2 py-0.5 rounded text-xs border border-purple-500/30">{{ $package['validity'] ?? '' }} {{ $package['validity_type'] ?? '' }}</span>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">{{ $package['mikrotik_profile'] ?? 'N/A' }}</p>
                        </div>
                        <div class="flex space-x-1">
                            <button wire:click="editPackage({{ $package['id'] }})" class="text-indigo-400 hover:text-indigo-300 p-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            </button>
                            <button wire:click="deletePackage({{ $package['id'] }})" wire:confirm="এই প্যাকেজ ডিলিট করতে চান?" class="text-red-400 hover:text-red-300 p-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                        </div>
                    </div>
                </div>
                @empty
                <div class="p-12 text-center text-gray-500">কোনো প্যাকেজ নেই</div>
                @endforelse
            </div>

            <!-- Desktop Table -->
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-900/30">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase">নাম</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase">প্রোফাইল</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase">মূল্য</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase">মেয়াদ</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-400 uppercase">অ্যাকশন</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700/50">
                        @forelse($localPackages as $package)
                        <tr class="hover:bg-gray-700/30 transition-colors">
                            <td class="px-4 py-3 font-medium text-gray-200">{{ $package['name'] ?? 'N/A' }}</td>
                            <td class="px-4 py-3 text-sm text-indigo-400">{{ $package['mikrotik_profile'] ?? 'N/A' }}</td>
                            <td class="px-4 py-3 text-sm text-cyan-400">৳{{ number_format($package['price'] ?? 0) }}</td>
                            <td class="px-4 py-3 text-sm text-gray-400">{{ $package['validity'] ?? '' }} {{ $package['validity_type'] ?? '' }}</td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex justify-end space-x-2">
                                    <button wire:click="editPackage({{ $package['id'] }})" class="text-indigo-400 hover:text-indigo-300 p-1" title="এডিট">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </button>
                                    <button wire:click="deletePackage({{ $package['id'] }})" wire:confirm="এই প্যাকেজ ডিলিট করতে চান?" class="text-red-400 hover:text-red-300 p-1" title="ডিলিট">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="px-4 py-12 text-center text-gray-500">কোনো প্যাকেজ নেই</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    <!-- Create/Edit Profile Modal -->
    @if($showModal)
    <div class="fixed inset-0 bg-black/70 backdrop-blur-sm flex items-center justify-center z-50 p-4">
        <div class="bg-gradient-to-br from-gray-800 to-gray-900 rounded-xl shadow-2xl w-full max-w-md border border-gray-700/50">
            <div class="flex items-center justify-between p-4 border-b border-gray-700/50">
                <h3 class="text-lg font-semibold bg-gradient-to-r from-purple-400 to-pink-500 bg-clip-text text-transparent">{{ $editingId ? 'প্যাকেজ এডিট করুন' : 'নতুন প্যাকেজ' }}</h3>
                <button wire:click="$set('showModal', false)" class="text-gray-400 hover:text-gray-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            <form wire:submit="savePackage" class="p-4 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-1">নাম</label>
                    <input type="text" wire:model="formName" placeholder="Package Name" class="w-full border border-gray-600 rounded-lg px-4 py-2 bg-gray-700/50 text-gray-200 focus:ring-2 focus:ring-purple-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-1">MikroTik প্রোফাইল</label>
                    <select wire:model="formProfile" class="w-full border border-gray-600 rounded-lg px-4 py-2 bg-gray-700/50 text-gray-200 focus:ring-2 focus:ring-purple-500">
                        <option value="">প্রোফাইল নির্বাচন করুন</option>
                        @foreach($mikrotikProfiles as $profile)
                            <option value="{{ $profile['name'] }}">{{ $profile['name'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-1">মূল্য (৳)</label>
                        <input type="number" wire:model="formPrice" placeholder="100" class="w-full border border-gray-600 rounded-lg px-4 py-2 bg-gray-700/50 text-gray-200 focus:ring-2 focus:ring-purple-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-1">মেয়াদ</label>
                        <div class="flex space-x-2">
                            <input type="number" wire:model="formValidity" placeholder="1" class="w-full border border-gray-600 rounded-lg px-4 py-2 bg-gray-700/50 text-gray-200 focus:ring-2 focus:ring-purple-500">
                            <select wire:model="formValidityType" class="border border-gray-600 rounded-lg px-2 py-2 bg-gray-700/50 text-gray-200 focus:ring-2 focus:ring-purple-500">
                                <option value="hours">ঘণ্টা</option>
                                <option value="days">দিন</option>
                                <option value="weeks">সপ্তাহ</option>
                                <option value="months">মাস</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="flex justify-end space-x-3 pt-4">
                    <button type="button" wire:click="$set('showModal', false)" class="px-4 py-2 bg-gray-700 text-gray-300 rounded-lg hover:bg-gray-600 transition border border-gray-600">বাতিল</button>
                    <button type="submit" class="px-4 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-lg hover:from-indigo-500 hover:to-purple-500 transition">সেভ করুন</button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>
