<div>
    <!-- Header with Stats -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold bg-gradient-to-r from-cyan-400 to-blue-500 bg-clip-text text-transparent">
                📦 স্টক ইউজার ম্যানেজার
            </h1>
            <p class="text-gray-400 text-sm mt-1">আগে থেকে ইউজার জেনারেট করে রাখুন</p>
        </div>
        <button wire:click="openGenerateModal" 
                class="mt-4 md:mt-0 inline-flex items-center px-6 py-3 bg-gradient-to-r from-emerald-500 to-teal-600 text-white font-semibold rounded-xl hover:from-emerald-600 hover:to-teal-700 transition shadow-lg shadow-emerald-500/30">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
            জেনারেট করুন
        </button>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-gradient-to-br from-blue-600/20 to-blue-800/20 border border-blue-500/30 rounded-2xl p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-300 text-sm">মোট স্টক</p>
                    <p class="text-3xl font-bold text-white mt-1">{{ number_format($stats['total']) }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-500/20 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-emerald-600/20 to-emerald-800/20 border border-emerald-500/30 rounded-2xl p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-emerald-300 text-sm">ফ্রি/Available</p>
                    <p class="text-3xl font-bold text-white mt-1">{{ number_format($stats['available']) }}</p>
                </div>
                <div class="w-12 h-12 bg-emerald-500/20 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-amber-600/20 to-amber-800/20 border border-amber-500/30 rounded-2xl p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-amber-300 text-sm">অ্যাসাইন করা</p>
                    <p class="text-3xl font-bold text-white mt-1">{{ number_format($stats['assigned']) }}</p>
                </div>
                <div class="w-12 h-12 bg-amber-500/20 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-red-600/20 to-red-800/20 border border-red-500/30 rounded-2xl p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-red-300 text-sm">মেয়াদ শেষ</p>
                    <p class="text-3xl font-bold text-white mt-1">{{ number_format($stats['expired']) }}</p>
                </div>
                <div class="w-12 h-12 bg-red-500/20 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-gray-800/50 backdrop-blur border border-gray-700/50 rounded-2xl p-4 mb-6">
        <div class="flex flex-col md:flex-row gap-4">
            <div class="flex-1">
                <input type="text" wire:model.live="search" placeholder="ইউজারনেম খুঁজুন..."
                       class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600 rounded-xl text-white placeholder-gray-400 focus:outline-none focus:border-cyan-500">
            </div>
            <select wire:model.live="selectedRouter" 
                    class="px-4 py-3 bg-gray-700/50 border border-gray-600 rounded-xl text-white focus:outline-none focus:border-cyan-500">
                <option value="">সব রাউটার</option>
                @foreach($routers as $router)
                <option value="{{ $router->id }}">{{ $router->name }}</option>
                @endforeach
            </select>
            <select wire:model.live="statusFilter"
                    class="px-4 py-3 bg-gray-700/50 border border-gray-600 rounded-xl text-white focus:outline-none focus:border-cyan-500">
                <option value="">সব স্ট্যাটাস</option>
                <option value="available">Available</option>
                <option value="assigned">Assigned</option>
                <option value="expired">Expired</option>
            </select>
            @if($stats['available'] > 0)
            <button wire:click="deleteAllAvailable" wire:confirm="সব Available ইউজার মুছে ফেলতে চান?"
                    class="px-4 py-3 bg-red-600/20 border border-red-500/30 text-red-400 rounded-xl hover:bg-red-600/30 transition">
                সব মুছুন
            </button>
            @endif
        </div>
    </div>

    <!-- Users Table -->
    <div class="bg-gray-800/50 backdrop-blur border border-gray-700/50 rounded-2xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-900/50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-400 uppercase">ইউজারনেম</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-400 uppercase">পাসওয়ার্ড</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-400 uppercase">স্পীড</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-400 uppercase">মেয়াদ</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-400 uppercase">রাউটার</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-400 uppercase">স্ট্যাটাস</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-400 uppercase">তৈরি</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-gray-400 uppercase">অ্যাকশন</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700/50">
                    @forelse($stockUsers as $user)
                    <tr class="hover:bg-gray-700/30 transition">
                        <td class="px-6 py-4">
                            <span class="font-mono text-cyan-400 font-semibold">{{ $user->username }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="font-mono text-gray-300">{{ $user->password }}</span>
                        </td>
                        <td class="px-6 py-4 text-gray-300">{{ $user->speed_limit }}</td>
                        <td class="px-6 py-4 text-gray-300">{{ $user->validity_days }} দিন</td>
                        <td class="px-6 py-4 text-gray-300">{{ $user->router->name ?? '-' }}</td>
                        <td class="px-6 py-4">
                            @if($user->status === 'available')
                            <span class="px-3 py-1 bg-emerald-500/20 text-emerald-400 text-xs font-semibold rounded-full">Available</span>
                            @elseif($user->status === 'assigned')
                            <span class="px-3 py-1 bg-amber-500/20 text-amber-400 text-xs font-semibold rounded-full">Assigned</span>
                            @else
                            <span class="px-3 py-1 bg-red-500/20 text-red-400 text-xs font-semibold rounded-full">Expired</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-gray-400 text-sm">{{ $user->created_at->format('d M Y') }}</td>
                        <td class="px-6 py-4 text-right">
                            @if($user->status === 'available')
                            <button wire:click="deleteUser({{ $user->id }})" wire:confirm="মুছে ফেলতে চান?"
                                    class="p-2 text-red-400 hover:bg-red-500/20 rounded-lg transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                            <svg class="w-16 h-16 mx-auto mb-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                            </svg>
                            <p>কোন স্টক ইউজার নেই</p>
                            <button wire:click="openGenerateModal" class="mt-4 text-cyan-400 hover:text-cyan-300">
                                এখনই জেনারেট করুন →
                            </button>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($stockUsers->hasPages())
        <div class="px-6 py-4 border-t border-gray-700/50">
            {{ $stockUsers->links() }}
        </div>
        @endif
    </div>

    <!-- Generate Modal -->
    @if($showGenerateModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-sm">
        <div class="bg-gray-800 border border-gray-700 rounded-2xl w-full max-w-lg mx-4 p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold text-white">🔧 স্টক ইউজার জেনারেট</h3>
                <button wire:click="$set('showGenerateModal', false)" class="text-gray-400 hover:text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            
            @if($generating)
            <div class="text-center py-8">
                <div class="w-16 h-16 border-4 border-cyan-500 border-t-transparent rounded-full animate-spin mx-auto mb-4"></div>
                <p class="text-white text-lg mb-2">{{ $generationMessage }}</p>
                <div class="w-full bg-gray-700 rounded-full h-3">
                    <div class="bg-gradient-to-r from-cyan-500 to-blue-500 h-3 rounded-full transition-all" style="width: {{ $generationProgress }}%"></div>
                </div>
                <p class="text-gray-400 mt-2">{{ $generationProgress }}%</p>
            </div>
            @else
            <form wire:submit="generate" class="space-y-4">
                <div>
                    <label class="block text-sm text-gray-300 mb-2">রাউটার সিলেক্ট *</label>
                    <select wire:model="selectedRouter" required
                            class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-xl text-white focus:outline-none focus:border-cyan-500">
                        <option value="">রাউটার নির্বাচন করুন</option>
                        @foreach($routers as $router)
                        <option value="{{ $router->id }}">{{ $router->name }} ({{ $router->ip_address }})</option>
                        @endforeach
                    </select>
                    @error('selectedRouter') <span class="text-red-400 text-sm">{{ $message }}</span> @enderror
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm text-gray-300 mb-2">কতগুলো জেনারেট?</label>
                        <input type="number" wire:model="generateCount" min="1" max="500"
                               class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-xl text-white focus:outline-none focus:border-cyan-500">
                    </div>
                    <div>
                        <label class="block text-sm text-gray-300 mb-2">Prefix</label>
                        <input type="text" wire:model="usernamePrefix" maxlength="10"
                               class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-xl text-white focus:outline-none focus:border-cyan-500">
                    </div>
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm text-gray-300 mb-2">স্পীড লিমিট</label>
                        <select wire:model="speedLimit"
                                class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-xl text-white focus:outline-none focus:border-cyan-500">
                            <option value="5M/5M">5 Mbps</option>
                            <option value="10M/10M">10 Mbps</option>
                            <option value="15M/15M">15 Mbps</option>
                            <option value="20M/20M">20 Mbps</option>
                            <option value="50M/50M">50 Mbps</option>
                            <option value="100M/100M">100 Mbps</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm text-gray-300 mb-2">মেয়াদ (দিন)</label>
                        <input type="number" wire:model="validityDays" min="1" max="365"
                               class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-xl text-white focus:outline-none focus:border-cyan-500">
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm text-gray-300 mb-2">MikroTik প্রোফাইল</label>
                    <input type="text" wire:model="profile" placeholder="default"
                           class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-xl text-white focus:outline-none focus:border-cyan-500">
                </div>
                
                <div class="flex gap-3 pt-4">
                    <button type="button" wire:click="$set('showGenerateModal', false)"
                            class="flex-1 px-4 py-3 bg-gray-700 text-gray-300 rounded-xl hover:bg-gray-600 transition">
                        বাতিল
                    </button>
                    <button type="submit"
                            class="flex-1 px-4 py-3 bg-gradient-to-r from-emerald-500 to-teal-600 text-white font-semibold rounded-xl hover:from-emerald-600 hover:to-teal-700 transition">
                        জেনারেট শুরু করুন
                    </button>
                </div>
            </form>
            @endif
        </div>
    </div>
    @endif
</div>
