<div wire:poll.30s="refreshData">
    @section('title', 'ড্যাশবোর্ড')

    <!-- Router Selector + Controls -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6">
        <div class="flex items-center gap-3 flex-wrap">
            <select wire:model.live="selectedRouter"
                    class="bg-gray-800 border border-gray-700 text-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-indigo-500 transition">
                @foreach($routers as $router)
                    <option value="{{ $router->id }}">{{ $router->name }} ({{ $router->ip_address }})</option>
                @endforeach
            </select>
            @if(isset($systemInfo['identity']))
            <div class="flex items-center gap-2 px-3 py-1.5 bg-emerald-500/10 border border-emerald-500/30 rounded-xl">
                <span class="w-2 h-2 bg-emerald-400 rounded-full animate-pulse"></span>
                <span class="text-emerald-400 text-xs font-semibold">{{ $systemInfo['identity'] }}</span>
            </div>
            @endif
        </div>
        <div class="flex items-center gap-3">
            <span class="hidden sm:flex items-center gap-1.5 text-xs text-gray-600">
                <svg class="w-3 h-3 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
                Auto 30s
            </span>
            <button wire:click="refreshData"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-semibold rounded-xl transition">
                <svg class="w-4 h-4" wire:loading.class="animate-spin" wire:target="refreshData" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                রিফ্রেশ
            </button>
        </div>
    </div>

    <!-- STATS ROW 1 -->
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-3 mb-4">
        <div class="col-span-2 lg:col-span-1 bg-gradient-to-br from-emerald-900/40 to-emerald-800/20 rounded-2xl p-5 border border-emerald-700/40 card-hover relative overflow-hidden">
            <div class="absolute -top-4 -right-4 w-20 h-20 bg-emerald-500/5 rounded-full"></div>
            <p class="text-emerald-400 text-xs font-bold uppercase tracking-wider">সক্রিয় ইউজার</p>
            <p class="text-4xl font-black text-white mt-2">{{ $stats['active_users'] ?? 0 }}</p>
            <p class="text-emerald-700 text-xs mt-1 flex items-center gap-1">
                <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-pulse"></span>Live MikroTik
            </p>
        </div>
        <div class="bg-gradient-to-br from-cyan-900/40 to-cyan-800/20 rounded-2xl p-5 border border-cyan-700/40 card-hover relative overflow-hidden">
            <div class="absolute -top-4 -right-4 w-20 h-20 bg-cyan-500/5 rounded-full"></div>
            <p class="text-cyan-400 text-xs font-bold uppercase tracking-wider">স্টক Available</p>
            <p class="text-3xl font-black text-white mt-2">{{ $stockStats['available'] ?? 0 }}</p>
            <p class="text-cyan-700 text-xs mt-1">মোট {{ $stockStats['total'] ?? 0 }}</p>
        </div>
        <div class="bg-gradient-to-br from-violet-900/40 to-violet-800/20 rounded-2xl p-5 border border-violet-700/40 card-hover relative overflow-hidden">
            <div class="absolute -top-4 -right-4 w-20 h-20 bg-violet-500/5 rounded-full"></div>
            <p class="text-violet-400 text-xs font-bold uppercase tracking-wider">অ্যাসাইন্ড</p>
            <p class="text-3xl font-black text-white mt-2">{{ $stockStats['assigned'] ?? 0 }}</p>
            <p class="text-violet-700 text-xs mt-1">Running</p>
        </div>
        <div class="bg-gradient-to-br from-blue-900/40 to-blue-800/20 rounded-2xl p-5 border border-blue-700/40 card-hover relative overflow-hidden">
            <div class="absolute -top-4 -right-4 w-20 h-20 bg-blue-500/5 rounded-full"></div>
            <p class="text-blue-400 text-xs font-bold uppercase tracking-wider">অব্যবহৃত ভাউচার</p>
            <p class="text-3xl font-black text-white mt-2">{{ $stats['unused_vouchers'] ?? 0 }}</p>
            <p class="text-blue-700 text-xs mt-1">ব্যবহৃত: {{ $stats['used_vouchers'] ?? 0 }}</p>
        </div>
        <div class="bg-gradient-to-br from-amber-900/40 to-amber-800/20 rounded-2xl p-5 border border-amber-700/40 card-hover relative overflow-hidden">
            <div class="absolute -top-4 -right-4 w-20 h-20 bg-amber-500/5 rounded-full"></div>
            <p class="text-amber-400 text-xs font-bold uppercase tracking-wider">আজকের বিক্রয়</p>
            <p class="text-3xl font-black text-white mt-2">৳{{ number_format($stats['today_sales'] ?? 0) }}</p>
            <p class="text-amber-700 text-xs mt-1">মাসে: ৳{{ number_format($stats['month_sales'] ?? 0) }}</p>
        </div>
    </div>

    <!-- STATS ROW 2 -->
    <div class="grid grid-cols-3 gap-3 mb-6">
        <div class="bg-gradient-to-br from-purple-900/40 to-purple-800/20 rounded-2xl p-4 border border-purple-700/40 card-hover text-center">
            <p class="text-purple-400 text-xs font-bold uppercase tracking-wider">ব্যবহৃত ভাউচার</p>
            <p class="text-2xl font-black text-white mt-1">{{ $stats['used_vouchers'] ?? 0 }}</p>
        </div>
        <div class="bg-gradient-to-br from-indigo-900/40 to-indigo-800/20 rounded-2xl p-4 border border-indigo-700/40 card-hover text-center">
            <p class="text-indigo-400 text-xs font-bold uppercase tracking-wider">মাসের বিক্রয়</p>
            <p class="text-2xl font-black text-white mt-1">৳{{ number_format($stats['month_sales'] ?? 0) }}</p>
        </div>
        <div class="bg-gradient-to-br from-red-900/40 to-red-800/20 rounded-2xl p-4 border border-red-700/40 card-hover text-center">
            <p class="text-red-400 text-xs font-bold uppercase tracking-wider">মেয়াদোত্তীর্ণ স্টক</p>
            <p class="text-2xl font-black text-white mt-1">{{ $stockStats['expired'] ?? 0 }}</p>
        </div>
    </div>

    <!-- MAIN 2-COLUMN -->
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-5 mb-5">

        <!-- System Info -->
        <div class="bg-gray-800/60 backdrop-blur rounded-2xl border border-gray-700/60 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-700/40 flex items-center justify-between">
                <h3 class="font-bold text-gray-200 flex items-center gap-2 text-sm">
                    <div class="w-7 h-7 rounded-lg bg-indigo-500/20 flex items-center justify-center">
                        <svg class="w-3.5 h-3.5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"/>
                        </svg>
                    </div>
                    সিস্টেম তথ্য
                </h3>
                @if(isset($systemInfo['identity']))
                <span class="text-xs text-emerald-400 font-bold bg-emerald-500/10 px-2.5 py-1 rounded-lg border border-emerald-500/30">
                    {{ $systemInfo['identity'] }}
                </span>
                @endif
            </div>
            <div class="p-5">
                @if(isset($systemInfo['error']))
                    <div class="p-4 bg-red-500/10 border border-red-500/30 rounded-xl text-red-400 text-sm">
                        {{ $systemInfo['error'] }}
                    </div>
                @else
                <div class="space-y-3 text-sm">
                    @foreach([
                        ['বোর্ড নাম', $systemInfo['board_name'] ?? 'N/A', 'text-gray-300'],
                        ['RouterOS ভার্সন', $systemInfo['version'] ?? 'N/A', 'text-gray-300'],
                        ['আপটাইম', $systemInfo['uptime'] ?? 'N/A', 'text-emerald-400'],
                        ['HDD ফ্রি স্পেস', $systemInfo['free_hdd'] ?? 'N/A', 'text-gray-300'],
                    ] as [$label, $value, $color])
                    <div class="flex justify-between py-2 border-b border-gray-700/30">
                        <span class="text-gray-500">{{ $label }}</span>
                        <span class="{{ $color }} font-medium">{{ $value }}</span>
                    </div>
                    @endforeach

                    <!-- CPU -->
                    <div class="py-2 border-b border-gray-700/30">
                        <div class="flex justify-between mb-2">
                            <span class="text-gray-500">CPU লোড</span>
                            <span class="font-bold {{ ($systemInfo['cpu_load'] ?? 0) > 80 ? 'text-red-400' : (($systemInfo['cpu_load'] ?? 0) > 50 ? 'text-amber-400' : 'text-emerald-400') }}">
                                {{ $systemInfo['cpu_load'] ?? 0 }}%
                            </span>
                        </div>
                        <div class="h-2 bg-gray-700 rounded-full overflow-hidden">
                            <div class="h-full rounded-full transition-all duration-700 {{ ($systemInfo['cpu_load'] ?? 0) > 80 ? 'bg-red-500' : (($systemInfo['cpu_load'] ?? 0) > 50 ? 'bg-amber-500' : 'bg-emerald-500') }}"
                                 style="width:{{ min($systemInfo['cpu_load'] ?? 0, 100) }}%"></div>
                        </div>
                    </div>

                    <!-- Memory -->
                    <div class="py-1">
                        <div class="flex justify-between mb-2">
                            <span class="text-gray-500">মেমরি</span>
                            <span class="text-gray-300 font-medium text-xs">{{ $systemInfo['free_memory'] ?? 'N/A' }} / {{ $systemInfo['total_memory'] ?? 'N/A' }}</span>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Active Users Real-time -->
        <div class="bg-gray-800/60 backdrop-blur rounded-2xl border border-gray-700/60 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-700/40 flex items-center justify-between">
                <h3 class="font-bold text-gray-200 flex items-center gap-2 text-sm">
                    <div class="w-7 h-7 rounded-lg bg-emerald-500/20 flex items-center justify-center">
                        <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    সক্রিয় ইউজার
                    <span class="px-2 py-0.5 bg-emerald-500/20 text-emerald-400 text-xs font-black rounded-full">
                        {{ count($activeUsers) }}
                    </span>
                </h3>
                <span class="text-xs text-gray-600 flex items-center gap-1.5">
                    <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-pulse"></span>Live
                </span>
            </div>
            @if(count($activeUsers) > 0)
            <div class="divide-y divide-gray-700/30 max-h-72 overflow-y-auto">
                @foreach($activeUsers as $user)
                <div class="flex items-center justify-between px-5 py-3 hover:bg-gray-700/20 transition-colors">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="w-8 h-8 rounded-xl bg-indigo-500/20 flex items-center justify-center flex-shrink-0">
                            <span class="text-indigo-400 text-xs font-black">{{ strtoupper(substr($user['user'] ?? 'U', 0, 1)) }}</span>
                        </div>
                        <div class="min-w-0">
                            <p class="text-gray-200 text-sm font-semibold truncate">{{ $user['user'] ?? 'N/A' }}</p>
                            <p class="text-gray-500 text-xs">{{ $user['address'] ?? '' }} · <span class="text-emerald-600">{{ $user['uptime'] ?? '' }}</span></p>
                        </div>
                    </div>
                    <button wire:click="kickUser('{{ $user['.id'] ?? '' }}')"
                            wire:confirm="এই ইউজারকে disconnect করবেন?"
                            class="flex-shrink-0 px-3 py-1.5 text-xs font-semibold text-red-400 bg-red-500/10 hover:bg-red-500/20 border border-red-500/30 rounded-lg transition ml-3">
                        কিক
                    </button>
                </div>
                @endforeach
            </div>
            <div class="px-5 py-3 border-t border-gray-700/40 bg-gray-900/30">
                <a href="{{ route('sessions') }}" class="text-indigo-400 hover:text-indigo-300 text-xs font-semibold flex items-center gap-1 transition">
                    সব সেশন দেখুন →
                </a>
            </div>
            @else
            <div class="flex flex-col items-center justify-center py-12 text-center">
                <div class="w-12 h-12 rounded-2xl bg-gray-700/50 flex items-center justify-center mb-3">
                    <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <p class="text-gray-500 text-sm font-medium">কোন সক্রিয় ইউজার নেই</p>
                <p class="text-gray-600 text-xs mt-1">কেউ কানেক্টেড নেই</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Quick Links -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        @foreach([
            ['রাউটার', route('routers'), 'emerald', 'M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01'],
            ['ভাউচার তৈরি', route('vouchers.generate'), 'purple', 'M12 6v6m0 0v6m0-6h6m-6 0H6'],
            ['হটস্পট ইউজার', route('hotspot.users'), 'indigo', 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z'],
            ['Sales Report', route('reports.sales'), 'amber', 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z'],
        ] as [$label, $route, $color, $path])
        <a href="{{ $route }}"
           class="flex items-center gap-3 p-4 bg-gray-800/40 hover:bg-gray-800/70 border border-gray-700/40 hover:border-{{ $color }}-500/30 rounded-2xl transition group">
            <div class="w-9 h-9 rounded-xl bg-{{ $color }}-500/20 group-hover:bg-{{ $color }}-500/30 flex items-center justify-center flex-shrink-0 transition">
                <svg class="w-4.5 h-4.5 text-{{ $color }}-400" style="width:18px;height:18px" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $path }}"/>
                </svg>
            </div>
            <span class="text-gray-400 group-hover:text-gray-200 text-sm font-medium transition truncate">{{ $label }}</span>
        </a>
        @endforeach
    </div>
</div>
