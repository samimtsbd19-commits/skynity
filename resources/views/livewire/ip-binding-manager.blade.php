<div>
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 space-y-4 md:space-y-0">
        <h1 class="text-xl md:text-2xl font-bold bg-gradient-to-r from-purple-400 to-pink-500 bg-clip-text text-transparent">IP Binding ও DHCP</h1>
        <div class="flex flex-wrap items-center gap-2">
            <button wire:click="loadData" class="bg-gray-700 hover:bg-gray-600 text-gray-300 px-3 md:px-4 py-2 rounded-lg flex items-center space-x-2 transition border border-gray-600 text-sm">
                <svg class="w-4 h-4 md:w-5 md:h-5" wire:loading.class="animate-spin" wire:target="loadData" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                <span>রিফ্রেশ</span>
            </button>
            <button wire:click="openModal()" class="bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-500 hover:to-purple-500 text-white px-3 md:px-4 py-2 rounded-lg flex items-center space-x-2 transition text-sm">
                <svg class="w-4 h-4 md:w-5 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                <span>নতুন Binding</span>
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

    <!-- Router Select & Search -->
    <div class="bg-gray-800/50 backdrop-blur-sm rounded-xl border border-gray-700/50 p-3 md:p-4 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
            <select wire:model.live="selectedRouter" class="w-full border border-gray-600 rounded-lg px-4 py-2 bg-gray-700/50 text-gray-200 focus:ring-2 focus:ring-purple-500">
                @foreach($routers as $router)
                    <option value="{{ $router->id }}">{{ $router->name }}</option>
                @endforeach
            </select>
            <input type="text" wire:model.live.debounce.300ms="searchMac" placeholder="MAC বা কমেন্ট খুঁজুন..." class="w-full border border-gray-600 rounded-lg px-4 py-2 bg-gray-700/50 text-gray-200 placeholder-gray-500 focus:ring-2 focus:ring-purple-500">
            <div class="flex space-x-2">
                <button wire:click="$set('showTab', 'bindings')" class="flex-1 px-4 py-2 rounded-lg transition {{ $showTab === 'bindings' ? 'bg-gradient-to-r from-indigo-600 to-purple-600 text-white' : 'bg-gray-700 text-gray-300 border border-gray-600' }}">
                    IP Binding ({{ count($bindings) }})
                </button>
                <button wire:click="$set('showTab', 'leases')" class="flex-1 px-4 py-2 rounded-lg transition {{ $showTab === 'leases' ? 'bg-gradient-to-r from-indigo-600 to-purple-600 text-white' : 'bg-gray-700 text-gray-300 border border-gray-600' }}">
                    DHCP Leases ({{ count($dhcpLeases) }})
                </button>
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
        <!-- IP Bindings Table -->
        @if($showTab === 'bindings')
        <div class="bg-gray-800/50 backdrop-blur-sm rounded-xl border border-gray-700/50 overflow-hidden">
            <!-- Mobile Cards -->
            <div class="md:hidden divide-y divide-gray-700/50">
                @forelse($bindings as $binding)
                <div class="p-4 hover:bg-gray-700/30">
                    <div class="flex justify-between items-start mb-2">
                        <div>
                            <p class="font-mono text-sm text-cyan-400">{{ $binding['mac_address'] }}</p>
                            <p class="text-gray-400 text-sm">{{ $binding['address'] }}</p>
                        </div>
                        @if($binding['type'] === 'bypassed')
                            <span class="bg-emerald-500/20 text-emerald-400 px-2 py-1 rounded text-xs border border-emerald-500/30">Bypass</span>
                        @elseif($binding['type'] === 'blocked')
                            <span class="bg-red-500/20 text-red-400 px-2 py-1 rounded text-xs border border-red-500/30">Block</span>
                        @else
                            <span class="bg-gray-500/20 text-gray-400 px-2 py-1 rounded text-xs border border-gray-500/30">{{ $binding['type'] }}</span>
                        @endif
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-500 text-sm">{{ $binding['comment'] }}</span>
                        <button wire:click="delete('{{ $binding['id'] }}')" wire:confirm="এই Binding ডিলিট করতে চান?" class="text-red-400 hover:text-red-300 p-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        </button>
                    </div>
                </div>
                @empty
                <div class="p-12 text-center text-gray-500">কোনো IP Binding নেই</div>
                @endforelse
            </div>

            <!-- Desktop Table -->
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-900/50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase">MAC Address</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase">IP Address</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase">টাইপ</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase">মন্তব্য</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-400 uppercase">অ্যাকশন</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700/50">
                        @forelse($bindings as $binding)
                        <tr class="hover:bg-gray-700/30 transition-colors">
                            <td class="px-4 py-3 font-mono text-sm text-cyan-400">{{ $binding['mac_address'] }}</td>
                            <td class="px-4 py-3 text-gray-400">{{ $binding['address'] }}</td>
                            <td class="px-4 py-3">
                                @if($binding['type'] === 'bypassed')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">Bypass</span>
                                @elseif($binding['type'] === 'blocked')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-500/20 text-red-400 border border-red-500/30">Block</span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-500/20 text-gray-400 border border-gray-500/30">{{ $binding['type'] }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-gray-400">{{ $binding['comment'] }}</td>
                            <td class="px-4 py-3 text-right">
                                <button wire:click="delete('{{ $binding['id'] }}')" wire:confirm="এই Binding ডিলিট করতে চান?" class="text-red-400 hover:text-red-300 p-1" title="ডিলিট">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="px-4 py-12 text-center text-gray-500">কোনো IP Binding নেই</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        <!-- DHCP Leases Table -->
        @if($showTab === 'leases')
        <div class="bg-gray-800/50 backdrop-blur-sm rounded-xl border border-gray-700/50 overflow-hidden">
            <!-- Mobile Cards -->
            <div class="md:hidden divide-y divide-gray-700/50">
                @forelse($dhcpLeases as $lease)
                <div class="p-4 hover:bg-gray-700/30">
                    <div class="flex justify-between items-start mb-2">
                        <div>
                            <p class="text-gray-200 font-medium">{{ $lease['host_name'] }}</p>
                            <p class="font-mono text-xs text-cyan-400">{{ $lease['mac_address'] }}</p>
                            <p class="text-gray-400 text-sm">{{ $lease['address'] }}</p>
                        </div>
                        @if($lease['status'] === 'bound')
                            <span class="bg-emerald-500/20 text-emerald-400 px-2 py-1 rounded text-xs border border-emerald-500/30">Bound</span>
                        @else
                            <span class="bg-gray-500/20 text-gray-400 px-2 py-1 rounded text-xs border border-gray-500/30">{{ $lease['status'] }}</span>
                        @endif
                    </div>
                    <div class="flex justify-between items-center mt-2">
                        <span class="text-gray-500 text-xs">{{ $lease['expires_after'] }}</span>
                        <div class="flex space-x-2">
                            <button wire:click="bypassMac('{{ $lease['mac_address'] }}')" class="bg-emerald-500/20 text-emerald-400 hover:bg-emerald-500/30 px-2 py-1 rounded text-xs border border-emerald-500/30">Bypass</button>
                            <button wire:click="blockMac('{{ $lease['mac_address'] }}')" class="bg-red-500/20 text-red-400 hover:bg-red-500/30 px-2 py-1 rounded text-xs border border-red-500/30">Block</button>
                        </div>
                    </div>
                </div>
                @empty
                <div class="p-12 text-center text-gray-500">কোনো DHCP Lease নেই</div>
                @endforelse
            </div>

            <!-- Desktop Table -->
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-900/50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase">Host Name</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase">MAC Address</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase">IP Address</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase">স্ট্যাটাস</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase">মেয়াদ</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-400 uppercase">অ্যাকশন</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700/50">
                        @forelse($dhcpLeases as $lease)
                        <tr class="hover:bg-gray-700/30 transition-colors">
                            <td class="px-4 py-3 text-gray-200">{{ $lease['host_name'] }}</td>
                            <td class="px-4 py-3 font-mono text-sm text-cyan-400">{{ $lease['mac_address'] }}</td>
                            <td class="px-4 py-3 text-gray-400">{{ $lease['address'] }}</td>
                            <td class="px-4 py-3">
                                @if($lease['status'] === 'bound')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">Bound</span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-500/20 text-gray-400 border border-gray-500/30">{{ $lease['status'] }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-gray-500 text-sm">{{ $lease['expires_after'] }}</td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex justify-end space-x-2">
                                    <button wire:click="bypassMac('{{ $lease['mac_address'] }}')" class="bg-emerald-500/20 text-emerald-400 hover:bg-emerald-500/30 px-2 py-1 rounded text-xs transition border border-emerald-500/30">Bypass</button>
                                    <button wire:click="blockMac('{{ $lease['mac_address'] }}')" class="bg-red-500/20 text-red-400 hover:bg-red-500/30 px-2 py-1 rounded text-xs transition border border-red-500/30">Block</button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="px-4 py-12 text-center text-gray-500">কোনো DHCP Lease নেই</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    @endif

    <!-- Create Binding Modal -->
    @if($showModal)
    <div class="fixed inset-0 bg-black/70 backdrop-blur-sm flex items-center justify-center z-50 p-4">
        <div class="bg-gradient-to-br from-gray-800 to-gray-900 rounded-xl shadow-2xl w-full max-w-md border border-gray-700/50">
            <div class="flex items-center justify-between p-4 border-b border-gray-700/50">
                <h3 class="text-lg font-semibold bg-gradient-to-r from-purple-400 to-pink-500 bg-clip-text text-transparent">নতুন IP Binding</h3>
                <button wire:click="$set('showModal', false)" class="text-gray-400 hover:text-gray-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            <form wire:submit.prevent="save" class="p-4 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-1">MAC Address</label>
                    <input type="text" wire:model="formData.mac_address" placeholder="00:00:00:00:00:00" class="w-full border border-gray-600 rounded-lg px-4 py-2 bg-gray-700/50 text-gray-200 focus:ring-2 focus:ring-purple-500 font-mono">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-1">টাইপ</label>
                    <select wire:model="formData.type" class="w-full border border-gray-600 rounded-lg px-4 py-2 bg-gray-700/50 text-gray-200 focus:ring-2 focus:ring-purple-500">
                        <option value="bypassed">Bypass (Free Access)</option>
                        <option value="blocked">Block (No Access)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-1">মন্তব্য</label>
                    <input type="text" wire:model="formData.comment" placeholder="Device name or note" class="w-full border border-gray-600 rounded-lg px-4 py-2 bg-gray-700/50 text-gray-200 focus:ring-2 focus:ring-purple-500">
                </div>
                <div class="flex justify-end space-x-3 pt-4">
                    <button type="button" wire:click="$set('showModal', false)" class="px-4 py-2 bg-gray-700 text-gray-300 rounded-lg hover:bg-gray-600 transition border border-gray-600">বাতিল</button>
                    <button type="submit" class="px-4 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-lg hover:from-indigo-500 hover:to-purple-500 transition">তৈরি করুন</button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>
