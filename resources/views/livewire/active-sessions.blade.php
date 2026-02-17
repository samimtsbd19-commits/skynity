<div wire:poll.30s="loadSessions">
    @section('title', 'অ্যাক্টিভ সেশন')

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-xl font-bold text-white">অ্যাক্টিভ সেশন</h1>
            <p class="text-gray-500 text-sm mt-0.5">Real-time MikroTik সংযুক্ত ইউজার</p>
        </div>
        <div class="flex items-center gap-2">
            <span class="hidden sm:flex items-center gap-1.5 text-xs text-gray-600 mr-1">
                <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-pulse"></span>
                Auto 30s
            </span>
            <button wire:click="loadSessions"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-semibold rounded-xl transition">
                <svg class="w-4 h-4" wire:loading.class="animate-spin" wire:target="loadSessions" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                রিফ্রেশ
            </button>
            @if(count($sessions) > 0)
            <button wire:click="kickAll" wire:confirm="সব ইউজার disconnect করতে চান?"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-red-600/80 hover:bg-red-600 text-white text-sm font-semibold rounded-xl transition border border-red-500/30">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                </svg>
                <span class="hidden sm:inline">সব কিক</span>
            </button>
            @endif
        </div>
    </div>

    <!-- Flash -->
    @if (session()->has('message'))
        <div class="mb-4 p-4 bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 rounded-xl text-sm">{{ session('message') }}</div>
    @endif

    <!-- Stats -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-6">
        <div class="bg-gradient-to-br from-indigo-900/40 to-indigo-800/20 rounded-2xl p-4 border border-indigo-700/40">
            <p class="text-indigo-400 text-xs font-bold uppercase tracking-wider">মোট সেশন</p>
            <p class="text-2xl font-black text-white mt-1.5">{{ $totalSessions }}</p>
        </div>
        <div class="bg-gradient-to-br from-emerald-900/40 to-emerald-800/20 rounded-2xl p-4 border border-emerald-700/40">
            <p class="text-emerald-400 text-xs font-bold uppercase tracking-wider">আপলোড</p>
            <p class="text-2xl font-black text-white mt-1.5 text-base">{{ $totalUpload }}</p>
        </div>
        <div class="bg-gradient-to-br from-blue-900/40 to-blue-800/20 rounded-2xl p-4 border border-blue-700/40">
            <p class="text-blue-400 text-xs font-bold uppercase tracking-wider">ডাউনলোড</p>
            <p class="text-2xl font-black text-white mt-1.5 text-base">{{ $totalDownload }}</p>
        </div>
        <div class="bg-gray-800/60 rounded-2xl p-4 border border-gray-700/40 col-span-2 sm:col-span-1">
            <select wire:model.live="selectedRouter"
                    class="w-full border border-gray-700 rounded-xl px-3 py-2 bg-gray-900/50 text-gray-200 focus:outline-none focus:border-indigo-500 text-sm">
                @foreach($routers as $router)
                    <option value="{{ $router->id }}">{{ $router->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <!-- Search -->
    <div class="bg-gray-800/60 backdrop-blur rounded-2xl border border-gray-700/60 p-4 mb-5">
        <div class="relative">
            <div class="absolute inset-y-0 left-3.5 flex items-center pointer-events-none">
                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
            <input type="text" wire:model.live.debounce.300ms="searchUser"
                   placeholder="ইউজারনেম বা MAC দিয়ে খুঁজুন..."
                   class="w-full pl-10 pr-4 py-2.5 bg-gray-900/50 border border-gray-700 rounded-xl text-gray-200 text-sm placeholder-gray-500 focus:outline-none focus:border-indigo-500 transition">
        </div>
    </div>

    <!-- Sessions -->
    <div class="bg-gray-800/60 backdrop-blur rounded-2xl border border-gray-700/60 overflow-hidden">
        @if($isLoading)
        <div class="flex flex-col items-center justify-center py-16">
            <div class="w-10 h-10 border-2 border-indigo-500 border-t-transparent rounded-full animate-spin mb-4"></div>
            <p class="text-gray-400 text-sm">MikroTik থেকে লোড হচ্ছে...</p>
        </div>
        @else

        <!-- MOBILE CARDS -->
        <div class="md:hidden divide-y divide-gray-700/40">
            @forelse($sessions as $session)
            <div class="p-4 hover:bg-gray-700/20 transition-colors">
                <div class="flex items-start justify-between mb-3">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-indigo-500/20 border border-indigo-500/30 flex items-center justify-center flex-shrink-0">
                            <span class="text-indigo-400 font-black text-sm">{{ strtoupper(substr($session['user'], 0, 1)) }}</span>
                        </div>
                        <div>
                            <p class="font-bold text-cyan-400 text-sm">{{ $session['user'] }}</p>
                            <p class="font-mono text-gray-600 text-xs">{{ $session['mac_address'] }}</p>
                        </div>
                    </div>
                    <button wire:click="kickUser('{{ $session['id'] }}')"
                            wire:confirm="এই ইউজার disconnect করবেন?"
                            class="px-3 py-1.5 bg-red-500/10 text-red-400 border border-red-500/30 rounded-xl text-xs font-bold hover:bg-red-500/20 transition">
                        কিক
                    </button>
                </div>
                <div class="grid grid-cols-2 gap-2 text-xs">
                    <div class="bg-gray-900/40 rounded-lg p-2">
                        <span class="text-gray-600">IP</span>
                        <p class="text-gray-300 font-mono mt-0.5">{{ $session['address'] }}</p>
                    </div>
                    <div class="bg-gray-900/40 rounded-lg p-2">
                        <span class="text-gray-600">আপটাইম</span>
                        <p class="text-emerald-400 font-semibold mt-0.5">{{ $session['uptime'] }}</p>
                    </div>
                    <div class="bg-gray-900/40 rounded-lg p-2">
                        <span class="text-gray-600">↑ আপলোড</span>
                        <p class="text-green-400 font-semibold mt-0.5">{{ $session['bytes_in'] }}</p>
                    </div>
                    <div class="bg-gray-900/40 rounded-lg p-2">
                        <span class="text-gray-600">↓ ডাউনলোড</span>
                        <p class="text-blue-400 font-semibold mt-0.5">{{ $session['bytes_out'] }}</p>
                    </div>
                </div>
            </div>
            @empty
            <div class="flex flex-col items-center justify-center py-16 text-center">
                <div class="w-14 h-14 rounded-2xl bg-gray-700/50 flex items-center justify-center mb-4">
                    <svg class="w-7 h-7 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <p class="text-gray-500 font-medium">কোনো অ্যাক্টিভ সেশন নেই</p>
            </div>
            @endforelse
        </div>

        <!-- DESKTOP TABLE -->
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-900/50">
                    <tr class="text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">
                        <th class="px-5 py-4">ইউজার</th>
                        <th class="px-5 py-4">IP</th>
                        <th class="px-5 py-4">MAC</th>
                        <th class="px-5 py-4">আপটাইম</th>
                        <th class="px-5 py-4">↑ আপলোড</th>
                        <th class="px-5 py-4">↓ ডাউনলোড</th>
                        <th class="px-5 py-4 text-right">অ্যাকশন</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700/30">
                    @forelse($sessions as $session)
                    <tr class="hover:bg-gray-700/20 transition-colors">
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-xl bg-indigo-500/20 flex items-center justify-center flex-shrink-0">
                                    <span class="text-indigo-400 text-xs font-black">{{ strtoupper(substr($session['user'], 0, 1)) }}</span>
                                </div>
                                <span class="font-semibold text-cyan-400 text-sm">{{ $session['user'] }}</span>
                            </div>
                        </td>
                        <td class="px-5 py-4 font-mono text-gray-400 text-sm">{{ $session['address'] }}</td>
                        <td class="px-5 py-4 font-mono text-gray-600 text-xs">{{ $session['mac_address'] }}</td>
                        <td class="px-5 py-4 text-emerald-400 font-semibold text-sm">{{ $session['uptime'] }}</td>
                        <td class="px-5 py-4 text-green-400 text-sm">{{ $session['bytes_in'] }}</td>
                        <td class="px-5 py-4 text-blue-400 text-sm">{{ $session['bytes_out'] }}</td>
                        <td class="px-5 py-4 text-right">
                            <button wire:click="kickUser('{{ $session['id'] }}')"
                                    wire:confirm="এই ইউজার disconnect করবেন?"
                                    class="px-3 py-1.5 bg-red-500/10 text-red-400 border border-red-500/30 rounded-xl text-xs font-bold hover:bg-red-500/20 transition">
                                কিক
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-5 py-16 text-center">
                            <div class="w-14 h-14 rounded-2xl bg-gray-700/50 flex items-center justify-center mb-4 mx-auto">
                                <svg class="w-7 h-7 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </div>
                            <p class="text-gray-500 font-medium">কোনো অ্যাক্টিভ সেশন নেই</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @endif
    </div>
</div>
