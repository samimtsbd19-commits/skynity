<div wire:poll.10s="loadTrafficData">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 space-y-4 md:space-y-0">
        <h1 class="text-xl md:text-2xl font-bold bg-gradient-to-r from-purple-400 to-pink-500 bg-clip-text text-transparent">ট্রাফিক মনিটর</h1>
        <div class="flex flex-wrap items-center gap-2">
            <select wire:model.live="selectedRouter" class="border border-gray-600 rounded-lg px-4 py-2 bg-gray-700/50 text-gray-200 focus:ring-2 focus:ring-purple-500 text-sm">
                @foreach($routers as $router)
                    <option value="{{ $router->id }}">{{ $router->name }}</option>
                @endforeach
            </select>
            <button wire:click="loadTrafficData" class="bg-gray-700 hover:bg-gray-600 text-gray-300 px-3 md:px-4 py-2 rounded-lg flex items-center space-x-2 transition border border-gray-600 text-sm">
                <svg class="w-4 h-4 md:w-5 md:h-5" wire:loading.class="animate-spin" wire:target="loadTrafficData" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                <span>রিফ্রেশ</span>
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

    @if($isLoading)
        <div class="bg-gray-800/50 rounded-xl border border-gray-700/50 p-12 text-center">
            <svg class="animate-spin h-8 w-8 text-purple-500 mx-auto" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <p class="mt-2 text-gray-400">লোড হচ্ছে...</p>
        </div>
    @else
    <!-- System Resource Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 md:gap-4 mb-6">
        <!-- CPU -->
        <div class="bg-gradient-to-br from-indigo-600/20 to-purple-600/20 rounded-xl p-3 md:p-4 border border-indigo-500/30">
            <div class="flex items-center justify-between mb-2">
                <span class="text-gray-400 text-xs md:text-sm">CPU</span>
                <svg class="w-4 h-4 md:w-5 md:h-5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"></path>
                </svg>
            </div>
            <p class="text-2xl md:text-3xl font-bold text-indigo-400">{{ $systemData['cpu_load'] ?? 0 }}%</p>
            <div class="mt-2 bg-gray-700/50 rounded-full h-2">
                <div class="bg-gradient-to-r from-indigo-500 to-purple-500 rounded-full h-2 transition-all" style="width: {{ $systemData['cpu_load'] ?? 0 }}%"></div>
            </div>
        </div>

        <!-- Memory -->
        <div class="bg-gradient-to-br from-emerald-600/20 to-teal-600/20 rounded-xl p-3 md:p-4 border border-emerald-500/30">
            <div class="flex items-center justify-between mb-2">
                <span class="text-gray-400 text-xs md:text-sm">মেমোরি</span>
                <svg class="w-4 h-4 md:w-5 md:h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                </svg>
            </div>
            <p class="text-2xl md:text-3xl font-bold text-emerald-400">{{ $systemData['memory_percent'] ?? 0 }}%</p>
            <div class="mt-2 bg-gray-700/50 rounded-full h-2">
                <div class="bg-gradient-to-r from-emerald-500 to-teal-500 rounded-full h-2 transition-all" style="width: {{ $systemData['memory_percent'] ?? 0 }}%"></div>
            </div>
            <p class="text-xs text-gray-500 mt-1">{{ $systemData['free_memory'] ?? 'N/A' }} ফ্রি</p>
        </div>

        <!-- HDD -->
        <div class="bg-gradient-to-br from-cyan-600/20 to-blue-600/20 rounded-xl p-3 md:p-4 border border-cyan-500/30">
            <div class="flex items-center justify-between mb-2">
                <span class="text-gray-400 text-xs md:text-sm">HDD</span>
                <svg class="w-4 h-4 md:w-5 md:h-5 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"></path>
                </svg>
            </div>
            <p class="text-2xl md:text-3xl font-bold text-cyan-400">{{ $systemData['hdd_percent'] ?? 0 }}%</p>
            <div class="mt-2 bg-gray-700/50 rounded-full h-2">
                <div class="bg-gradient-to-r from-cyan-500 to-blue-500 rounded-full h-2 transition-all" style="width: {{ $systemData['hdd_percent'] ?? 0 }}%"></div>
            </div>
            <p class="text-xs text-gray-500 mt-1">{{ $systemData['free_hdd'] ?? 'N/A' }} ফ্রি</p>
        </div>

        <!-- Uptime -->
        <div class="bg-gradient-to-br from-amber-600/20 to-orange-600/20 rounded-xl p-3 md:p-4 border border-amber-500/30">
            <div class="flex items-center justify-between mb-2">
                <span class="text-gray-400 text-xs md:text-sm">আপটাইম</span>
                <svg class="w-4 h-4 md:w-5 md:h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <p class="text-lg md:text-xl font-bold text-amber-400">{{ $systemData['uptime'] ?? 'N/A' }}</p>
            <p class="text-xs text-gray-500 mt-1">{{ $systemData['board_name'] ?? 'RouterBoard' }}</p>
        </div>
    </div>

    <!-- System Info Details -->
    <div class="bg-gray-800/50 backdrop-blur-sm rounded-xl border border-gray-700/50 p-4 mb-6">
        <h2 class="text-lg font-semibold text-gray-200 mb-4 flex items-center space-x-2">
            <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span>সিস্টেম তথ্য</span>
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="flex justify-between">
                <span class="text-gray-400">মডেল:</span>
                <span class="text-gray-200">{{ $systemData['board_name'] ?? 'N/A' }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-400">ভার্সন:</span>
                <span class="text-gray-200">{{ $systemData['version'] ?? 'N/A' }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-400">আর্কিটেকচার:</span>
                <span class="text-gray-200">{{ $systemData['architecture'] ?? 'N/A' }}</span>
            </div>
        </div>
    </div>

    <!-- Interface Traffic -->
    <div class="bg-gray-800/50 backdrop-blur-sm rounded-xl border border-gray-700/50 overflow-hidden mb-6">
        <div class="bg-gray-900/50 px-4 py-3 border-b border-gray-700/50">
            <h2 class="text-lg font-semibold text-gray-200 flex items-center space-x-2">
                <svg class="w-5 h-5 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                </svg>
                <span>ইন্টারফেস ট্রাফিক</span>
            </h2>
        </div>
        
        <!-- Mobile Cards -->
        <div class="md:hidden divide-y divide-gray-700/50">
            @forelse($interfaceData as $interface)
            <div class="p-4 hover:bg-gray-700/30">
                <div class="flex justify-between items-start mb-2">
                    <div>
                        <p class="font-medium text-gray-200">{{ $interface['name'] }}</p>
                        <p class="text-xs text-gray-500">{{ $interface['type'] ?? 'ethernet' }}</p>
                    </div>
                    @if($interface['running'] ?? false)
                        <span class="bg-emerald-500/20 text-emerald-400 px-2 py-1 rounded text-xs border border-emerald-500/30">Running</span>
                    @else
                        <span class="bg-gray-500/20 text-gray-400 px-2 py-1 rounded text-xs border border-gray-500/30">Down</span>
                    @endif
                </div>
                <div class="grid grid-cols-2 gap-2 mt-2">
                    <div class="bg-emerald-500/10 rounded p-2 text-center">
                        <p class="text-xs text-gray-500">↓ RX</p>
                        <p class="text-sm font-medium text-emerald-400">{{ $interface['rx_byte'] ?? '0 B' }}</p>
                    </div>
                    <div class="bg-cyan-500/10 rounded p-2 text-center">
                        <p class="text-xs text-gray-500">↑ TX</p>
                        <p class="text-sm font-medium text-cyan-400">{{ $interface['tx_byte'] ?? '0 B' }}</p>
                    </div>
                </div>
            </div>
            @empty
            <div class="p-12 text-center text-gray-500">কোনো ইন্টারফেস নেই</div>
            @endforelse
        </div>

        <!-- Desktop Table -->
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-900/30">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase">ইন্টারফেস</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase">টাইপ</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase">স্ট্যাটাস</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-400 uppercase">↓ RX Total</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-400 uppercase">↑ TX Total</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-400 uppercase">RX Packets</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-400 uppercase">TX Packets</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700/50">
                    @forelse($interfaceData as $interface)
                    <tr class="hover:bg-gray-700/30 transition-colors">
                        <td class="px-4 py-3 font-medium text-gray-200">{{ $interface['name'] }}</td>
                        <td class="px-4 py-3 text-gray-400 text-sm">{{ $interface['type'] ?? 'ethernet' }}</td>
                        <td class="px-4 py-3">
                            @if($interface['running'] ?? false)
                                <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">Running</span>
                            @else
                                <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-gray-500/20 text-gray-400 border border-gray-500/30">Down</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right text-emerald-400 font-mono text-sm">{{ $interface['rx_byte'] ?? '0 B' }}</td>
                        <td class="px-4 py-3 text-right text-cyan-400 font-mono text-sm">{{ $interface['tx_byte'] ?? '0 B' }}</td>
                        <td class="px-4 py-3 text-right text-gray-400 font-mono text-sm">{{ number_format($interface['rx_packet'] ?? 0) }}</td>
                        <td class="px-4 py-3 text-right text-gray-400 font-mono text-sm">{{ number_format($interface['tx_packet'] ?? 0) }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="px-4 py-12 text-center text-gray-500">কোনো ইন্টারফেস নেই</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Queue Stats -->
    @if(count($queueData ?? []) > 0)
    <div class="bg-gray-800/50 backdrop-blur-sm rounded-xl border border-gray-700/50 overflow-hidden">
        <div class="bg-gray-900/50 px-4 py-3 border-b border-gray-700/50">
            <h2 class="text-lg font-semibold text-gray-200 flex items-center space-x-2">
                <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
                <span>Queue Stats</span>
            </h2>
        </div>

        <!-- Mobile Cards -->
        <div class="md:hidden divide-y divide-gray-700/50">
            @forelse($queueData as $queue)
            <div class="p-4 hover:bg-gray-700/30">
                <div class="flex justify-between items-start mb-2">
                    <p class="font-medium text-gray-200">{{ $queue['name'] }}</p>
                    <span class="text-xs text-gray-500">{{ $queue['target'] ?? 'N/A' }}</span>
                </div>
                <div class="grid grid-cols-2 gap-2 mt-2">
                    <div class="bg-emerald-500/10 rounded p-2">
                        <p class="text-xs text-gray-500">Max Limit</p>
                        <p class="text-sm font-medium text-emerald-400">{{ $queue['max_limit'] ?? 'unlimited' }}</p>
                    </div>
                    <div class="bg-cyan-500/10 rounded p-2">
                        <p class="text-xs text-gray-500">Download</p>
                        <p class="text-sm font-medium text-cyan-400">{{ $queue['download_rate'] ?? '0/s' }}</p>
                    </div>
                </div>
            </div>
            @empty
            <div class="p-12 text-center text-gray-500">কোনো Queue নেই</div>
            @endforelse
        </div>

        <!-- Desktop Table -->
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-900/30">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase">নাম</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase">Target</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-400 uppercase">Max Limit</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-400 uppercase">↑ Upload</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-400 uppercase">↓ Download</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-400 uppercase">Bytes</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700/50">
                    @forelse($queueData as $queue)
                    <tr class="hover:bg-gray-700/30 transition-colors">
                        <td class="px-4 py-3 font-medium text-gray-200">{{ $queue['name'] }}</td>
                        <td class="px-4 py-3 text-gray-400 text-sm">{{ $queue['target'] ?? 'N/A' }}</td>
                        <td class="px-4 py-3 text-right text-emerald-400 font-mono text-sm">{{ $queue['max_limit'] ?? 'unlimited' }}</td>
                        <td class="px-4 py-3 text-right text-purple-400 font-mono text-sm">{{ $queue['upload_rate'] ?? '0/s' }}</td>
                        <td class="px-4 py-3 text-right text-cyan-400 font-mono text-sm">{{ $queue['download_rate'] ?? '0/s' }}</td>
                        <td class="px-4 py-3 text-right text-gray-400 font-mono text-sm">{{ $queue['bytes'] ?? '0/0' }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="px-4 py-12 text-center text-gray-500">কোনো Queue নেই</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @endif
    @endif

    <!-- Auto-refresh indicator -->
    <div class="mt-4 text-center">
        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs bg-gray-800/50 text-gray-500 border border-gray-700/30">
            <span class="w-2 h-2 bg-emerald-500 rounded-full mr-2 animate-pulse"></span>
            প্রতি ১০ সেকেন্ডে অটো-রিফ্রেশ হচ্ছে
        </span>
    </div>
</div>
