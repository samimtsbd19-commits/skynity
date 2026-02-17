<div>
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 space-y-4 md:space-y-0">
        <h1 class="text-xl md:text-2xl font-bold bg-gradient-to-r from-purple-400 to-pink-500 bg-clip-text text-transparent">হটস্পট ইউজার</h1>
        <div class="flex flex-wrap items-center gap-2">
            <button wire:click="syncFromMikrotik" class="bg-gradient-to-r from-emerald-600 to-green-600 hover:from-emerald-500 hover:to-green-500 text-white px-3 md:px-4 py-2 rounded-lg flex items-center space-x-2 transition text-sm">
                <svg class="w-4 h-4 md:w-5 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                <span>সিঙ্ক</span>
            </button>
            <button wire:click="openCreateModal" class="bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-500 hover:to-purple-500 text-white px-3 md:px-4 py-2 rounded-lg flex items-center space-x-2 transition text-sm">
                <svg class="w-4 h-4 md:w-5 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                <span>নতুন ইউজার</span>
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

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 md:gap-4 mb-6">
        <div class="bg-gradient-to-br from-indigo-900/50 to-indigo-800/30 rounded-xl p-4 border border-indigo-700/50">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs md:text-sm text-indigo-400">মোট ইউজার</p>
                    <p class="text-xl md:text-2xl font-bold text-white mt-1">{{ $totalUsers }}</p>
                </div>
                <div class="bg-indigo-600/30 rounded-full p-2 md:p-3 hidden sm:block">
                    <svg class="w-5 h-5 md:w-6 md:h-6 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-emerald-900/50 to-emerald-800/30 rounded-xl p-4 border border-emerald-700/50">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs md:text-sm text-emerald-400">সক্রিয়</p>
                    <p class="text-xl md:text-2xl font-bold text-white mt-1">{{ $activeCount }}</p>
                </div>
                <div class="bg-emerald-600/30 rounded-full p-2 md:p-3 hidden sm:block">
                    <svg class="w-5 h-5 md:w-6 md:h-6 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-red-900/50 to-red-800/30 rounded-xl p-4 border border-red-700/50">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs md:text-sm text-red-400">নিষ্ক্রিয়</p>
                    <p class="text-xl md:text-2xl font-bold text-white mt-1">{{ $disabledCount }}</p>
                </div>
                <div class="bg-red-600/30 rounded-full p-2 md:p-3 hidden sm:block">
                    <svg class="w-5 h-5 md:w-6 md:h-6 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-gray-800/50 rounded-xl p-4 border border-gray-700/50 col-span-2 md:col-span-1">
            <select wire:model.live="selectedRouter" class="w-full border border-gray-600 rounded-lg px-3 py-2 bg-gray-700/50 text-gray-200 focus:ring-2 focus:ring-purple-500 text-sm">
                @foreach($routers as $router)
                    <option value="{{ $router->id }}">{{ $router->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-gray-800/50 backdrop-blur-sm rounded-xl border border-gray-700/50 p-3 md:p-4 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
            <input type="text" wire:model.live.debounce.300ms="searchUser" placeholder="ইউজার খুঁজুন..." class="w-full border border-gray-600 rounded-lg px-4 py-2 bg-gray-700/50 text-gray-200 placeholder-gray-500 focus:ring-2 focus:ring-purple-500">
            <select wire:model.live="filterProfile" class="w-full border border-gray-600 rounded-lg px-4 py-2 bg-gray-700/50 text-gray-200 focus:ring-2 focus:ring-purple-500">
                <option value="">সব প্রোফাইল</option>
                @foreach($profiles as $profile)
                    <option value="{{ $profile['name'] ?? '' }}">{{ $profile['name'] ?? 'N/A' }}</option>
                @endforeach
            </select>
            <button wire:click="loadUsers" class="bg-gray-700 hover:bg-gray-600 text-gray-300 px-4 py-2 rounded-lg flex items-center justify-center space-x-2 transition border border-gray-600">
                <svg class="w-5 h-5" wire:loading.class="animate-spin" wire:target="loadUsers" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                <span>রিফ্রেশ</span>
            </button>
        </div>
    </div>

    <!-- Users Table -->
    <div class="bg-gray-800/50 backdrop-blur-sm rounded-xl border border-gray-700/50 overflow-hidden">
        @if($isLoading)
            <div class="p-12 text-center">
                <svg class="animate-spin h-8 w-8 text-purple-500 mx-auto" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <p class="mt-2 text-gray-400">লোড হচ্ছে...</p>
            </div>
        @else
            <!-- Mobile Cards View -->
            <div class="md:hidden divide-y divide-gray-700/50">
                @forelse($users as $user)
                <div class="p-4 hover:bg-gray-700/30 {{ $user['disabled'] ? 'opacity-60' : '' }}">
                    <div class="flex justify-between items-start mb-2">
                        <div>
                            <p class="font-mono font-medium text-cyan-400">{{ $user['name'] }}</p>
                            <p class="text-xs text-gray-500">{{ $user['password'] }}</p>
                        </div>
                        <span class="bg-indigo-500/20 text-indigo-400 px-2 py-1 rounded text-xs border border-indigo-500/30">{{ $user['profile'] }}</span>
                    </div>
                    <div class="flex justify-between items-center mt-2">
                        <div class="text-sm">
                            @if($user['disabled'])
                                <span class="bg-red-500/20 text-red-400 px-2 py-1 rounded text-xs border border-red-500/30">নিষ্ক্রিয়</span>
                            @else
                                <span class="bg-emerald-500/20 text-emerald-400 px-2 py-1 rounded text-xs border border-emerald-500/30">সক্রিয়</span>
                            @endif
                        </div>
                        <div class="flex space-x-2">
                            <button wire:click="openEditModal(@json($user))" class="text-blue-400 hover:text-blue-300 p-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            </button>
                            <button wire:click="toggleUser('{{ $user['id'] }}', {{ $user['disabled'] ? 'false' : 'true' }})" class="text-yellow-400 hover:text-yellow-300 p-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path></svg>
                            </button>
                            <button wire:click="delete('{{ $user['id'] }}')" wire:confirm="এই ইউজার ডিলিট করতে চান?" class="text-red-400 hover:text-red-300 p-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                        </div>
                    </div>
                </div>
                @empty
                <div class="p-12 text-center">
                    <svg class="w-16 h-16 text-gray-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                    <p class="text-gray-500">কোনো ইউজার নেই</p>
                </div>
                @endforelse
            </div>

            <!-- Desktop Table View -->
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-900/50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase">ইউজারনেম</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase">পাসওয়ার্ড</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase">প্রোফাইল</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase">লিমিট</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase">স্ট্যাটাস</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-400 uppercase">অ্যাকশন</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700/50">
                        @forelse($users as $user)
                        <tr class="hover:bg-gray-700/30 transition-colors {{ $user['disabled'] ? 'opacity-60' : '' }}">
                            <td class="px-4 py-3">
                                <span class="font-mono font-medium text-cyan-400">{{ $user['name'] }}</span>
                            </td>
                            <td class="px-4 py-3 font-mono text-gray-400">{{ $user['password'] }}</td>
                            <td class="px-4 py-3">
                                <span class="bg-indigo-500/20 text-indigo-400 px-2 py-1 rounded text-sm border border-indigo-500/30">{{ $user['profile'] }}</span>
                            </td>
                            <td class="px-4 py-3 text-gray-400 text-sm">
                                @if($user['limit_uptime'])
                                    <span class="block">সময়: {{ $user['limit_uptime'] }}</span>
                                @endif
                                @if($user['limit_bytes'])
                                    <span class="block">ডাটা: {{ $user['limit_bytes'] }}</span>
                                @endif
                                @if(!$user['limit_uptime'] && !$user['limit_bytes'])
                                    <span class="text-gray-500">আনলিমিটেড</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @if($user['disabled'])
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-500/20 text-red-400 border border-red-500/30">নিষ্ক্রিয়</span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">সক্রিয়</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex items-center justify-end space-x-2">
                                    <button wire:click="openEditModal(@json($user))" class="text-blue-400 hover:text-blue-300 p-1" title="এডিট">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </button>
                                    <button wire:click="toggleUser('{{ $user['id'] }}', {{ $user['disabled'] ? 'false' : 'true' }})" class="text-yellow-400 hover:text-yellow-300 p-1" title="সক্রিয়/নিষ্ক্রিয়">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path></svg>
                                    </button>
                                    <button wire:click="delete('{{ $user['id'] }}')" wire:confirm="এই ইউজার ডিলিট করতে চান?" class="text-red-400 hover:text-red-300 p-1" title="ডিলিট">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-4 py-12 text-center">
                                <svg class="w-16 h-16 text-gray-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                </svg>
                                <p class="text-gray-500">কোনো ইউজার নেই</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <!-- Create/Edit User Modal -->
    @if($showModal)
    <div class="fixed inset-0 bg-black/70 backdrop-blur-sm flex items-center justify-center z-50 p-4">
        <div class="bg-gradient-to-br from-gray-800 to-gray-900 rounded-xl shadow-2xl w-full max-w-md border border-gray-700/50">
            <div class="flex items-center justify-between p-4 border-b border-gray-700/50">
                <h3 class="text-lg font-semibold bg-gradient-to-r from-purple-400 to-pink-500 bg-clip-text text-transparent">{{ $editMode ? 'ইউজার এডিট করুন' : 'নতুন ইউজার তৈরি করুন' }}</h3>
                <button wire:click="$set('showModal', false)" class="text-gray-400 hover:text-gray-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            <form wire:submit.prevent="save" class="p-4 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-1">ইউজারনেম</label>
                    <input type="text" wire:model="formData.name" class="w-full border border-gray-600 rounded-lg px-4 py-2 bg-gray-700/50 text-gray-200 focus:ring-2 focus:ring-purple-500" {{ $editMode ? 'readonly' : '' }}>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-1">পাসওয়ার্ড</label>
                    <input type="text" wire:model="formData.password" class="w-full border border-gray-600 rounded-lg px-4 py-2 bg-gray-700/50 text-gray-200 focus:ring-2 focus:ring-purple-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-1">প্রোফাইল</label>
                    <select wire:model="formData.profile" class="w-full border border-gray-600 rounded-lg px-4 py-2 bg-gray-700/50 text-gray-200 focus:ring-2 focus:ring-purple-500">
                        @foreach($profiles as $profile)
                            <option value="{{ $profile['name'] }}">{{ $profile['name'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex justify-end space-x-3 pt-4">
                    <button type="button" wire:click="$set('showModal', false)" class="px-4 py-2 bg-gray-700 text-gray-300 rounded-lg hover:bg-gray-600 transition border border-gray-600">বাতিল</button>
                    <button type="submit" class="px-4 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-lg hover:from-indigo-500 hover:to-purple-500 transition">{{ $editMode ? 'আপডেট করুন' : 'তৈরি করুন' }}</button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>
