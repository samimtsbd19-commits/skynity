<div>
    @section('title', 'রাউটার ম্যানেজমেন্ট')

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <h2 class="text-xl font-bold text-white">রাউটার ম্যানেজমেন্ট</h2>
            <p class="text-gray-500 text-sm mt-0.5">MikroTik রাউটার সংযোগ পরিচালনা করুন</p>
        </div>
        <button wire:click="openModal"
                class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-500 hover:to-purple-500 text-white text-sm font-semibold rounded-xl transition shadow-lg shadow-indigo-500/20">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
            নতুন রাউটার
        </button>
    </div>

    <!-- Flash Messages -->
    @if (session()->has('message'))
        <div class="mb-4 p-4 bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 rounded-xl text-sm">{{ session('message') }}</div>
    @endif

    <!-- ========================
         DESKTOP TABLE VIEW
    ======================== -->
    <div class="hidden md:block bg-gray-800/60 backdrop-blur rounded-2xl border border-gray-700/60 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-900/60 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">
                        <th class="px-5 py-4">রাউটার</th>
                        <th class="px-5 py-4">IP / পোর্ট</th>
                        <th class="px-5 py-4">হটস্পট</th>
                        <th class="px-5 py-4">স্ট্যাটাস</th>
                        <th class="px-5 py-4">শেষ সংযোগ</th>
                        <th class="px-5 py-4 text-right">অ্যাকশন</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700/40">
                    @forelse($routers as $router)
                        <tr class="hover:bg-gray-700/30 transition-colors">
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-indigo-500/20 to-purple-500/20 border border-indigo-500/30 flex items-center justify-center flex-shrink-0">
                                        <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-200 text-sm">{{ $router->name }}</p>
                                        @if($router->dns_name)
                                        <p class="text-xs text-gray-500">{{ $router->dns_name }}</p>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-4">
                                <p class="font-mono text-indigo-400 text-sm font-semibold">{{ $router->ip_address }}</p>
                                <p class="text-xs text-gray-500">Port: {{ $router->port }}</p>
                            </td>
                            <td class="px-5 py-4 text-gray-400 text-sm">{{ $router->hotspot_name ?: '—' }}</td>
                            <td class="px-5 py-4">
                                @if($router->is_active)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-emerald-500/15 text-emerald-400 text-xs font-semibold rounded-full border border-emerald-500/30">
                                        <span class="w-1.5 h-1.5 bg-emerald-400 rounded-full"></span>সক্রিয়
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-gray-700/50 text-gray-500 text-xs font-semibold rounded-full border border-gray-600/50">
                                        <span class="w-1.5 h-1.5 bg-gray-500 rounded-full"></span>নিষ্ক্রিয়
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-gray-500 text-sm">
                                {{ $router->last_connected_at ? $router->last_connected_at->diffForHumans() : 'কখনো নয়' }}
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-end gap-1">
                                    <button wire:click="testConnection({{ $router->id }})"
                                            class="p-2 text-emerald-400 hover:bg-emerald-500/15 rounded-lg transition" title="কানেকশন টেস্ট">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.141 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0"/>
                                        </svg>
                                    </button>
                                    <button wire:click="edit({{ $router->id }})"
                                            class="p-2 text-blue-400 hover:bg-blue-500/15 rounded-lg transition" title="এডিট">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </button>
                                    <button wire:click="toggleActive({{ $router->id }})"
                                            class="p-2 text-amber-400 hover:bg-amber-500/15 rounded-lg transition" title="সক্রিয়/নিষ্ক্রিয়">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                        </svg>
                                    </button>
                                    <button wire:click="delete({{ $router->id }})"
                                            wire:confirm="আপনি কি নিশ্চিত এই রাউটার ডিলিট করতে চান?"
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
                            <td colspan="6" class="px-5 py-16 text-center">
                                <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-gray-700/50 flex items-center justify-center">
                                    <svg class="w-8 h-8 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2"/>
                                    </svg>
                                </div>
                                <p class="text-gray-500 font-medium">কোন রাউটার নেই</p>
                                <p class="text-gray-600 text-sm mt-1">প্রথম রাউটার যোগ করুন</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($routers->hasPages())
        <div class="px-5 py-4 border-t border-gray-700/40">{{ $routers->links() }}</div>
        @endif
    </div>

    <!-- ========================
         MOBILE CARD VIEW
    ======================== -->
    <div class="md:hidden space-y-3">
        @forelse($routers as $router)
        <div class="bg-gray-800/60 backdrop-blur rounded-2xl border border-gray-700/60 p-4">
            <!-- Card Header -->
            <div class="flex items-start justify-between mb-3">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-500/20 to-purple-500/20 border border-indigo-500/30 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01"/>
                        </svg>
                    </div>
                    <div>
                        <p class="font-bold text-gray-200">{{ $router->name }}</p>
                        <p class="font-mono text-indigo-400 text-sm">{{ $router->ip_address }}:{{ $router->port }}</p>
                    </div>
                </div>
                @if($router->is_active)
                    <span class="px-2.5 py-1 bg-emerald-500/15 text-emerald-400 text-xs font-bold rounded-full border border-emerald-500/30">সক্রিয়</span>
                @else
                    <span class="px-2.5 py-1 bg-gray-700/50 text-gray-500 text-xs font-bold rounded-full border border-gray-600/50">নিষ্ক্রিয়</span>
                @endif
            </div>
            <!-- Info Row -->
            <div class="flex gap-4 text-xs text-gray-500 mb-4 pb-3 border-b border-gray-700/40">
                @if($router->hotspot_name)
                <span>Hotspot: <span class="text-gray-400">{{ $router->hotspot_name }}</span></span>
                @endif
                <span>শেষ সংযোগ: <span class="text-gray-400">{{ $router->last_connected_at ? $router->last_connected_at->diffForHumans() : 'কখনো নয়' }}</span></span>
            </div>
            <!-- Actions -->
            <div class="flex gap-2">
                <button wire:click="testConnection({{ $router->id }})"
                        class="flex-1 py-2 text-xs font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/30 rounded-xl hover:bg-emerald-500/20 transition flex items-center justify-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.141 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0"/>
                    </svg>
                    টেস্ট
                </button>
                <button wire:click="edit({{ $router->id }})"
                        class="flex-1 py-2 text-xs font-semibold bg-blue-500/10 text-blue-400 border border-blue-500/30 rounded-xl hover:bg-blue-500/20 transition flex items-center justify-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    এডিট
                </button>
                <button wire:click="toggleActive({{ $router->id }})"
                        class="flex-1 py-2 text-xs font-semibold bg-amber-500/10 text-amber-400 border border-amber-500/30 rounded-xl hover:bg-amber-500/20 transition flex items-center justify-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                    </svg>
                    টগল
                </button>
                <button wire:click="delete({{ $router->id }})"
                        wire:confirm="আপনি কি নিশ্চিত এই রাউটার ডিলিট করতে চান?"
                        class="p-2 text-red-400 hover:bg-red-500/15 rounded-xl transition border border-red-500/20">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </button>
            </div>
        </div>
        @empty
        <div class="bg-gray-800/60 rounded-2xl border border-gray-700/60 p-12 text-center">
            <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-gray-700/50 flex items-center justify-center">
                <svg class="w-8 h-8 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2"/>
                </svg>
            </div>
            <p class="text-gray-500 font-medium">কোন রাউটার নেই</p>
        </div>
        @endforelse

        @if($routers->hasPages())
        <div class="py-2">{{ $routers->links() }}</div>
        @endif
    </div>

    <!-- ========================
         ADD / EDIT MODAL
    ======================== -->
    @if($showModal)
    <div class="fixed inset-0 bg-black/70 backdrop-blur-sm flex items-end sm:items-center justify-center z-50 p-0 sm:p-4">
        <div class="bg-gray-900 rounded-t-3xl sm:rounded-2xl border border-gray-700/60 w-full sm:max-w-lg max-h-[90vh] overflow-y-auto shadow-2xl">
            <!-- Modal Header -->
            <div class="flex items-center justify-between px-6 py-5 border-b border-gray-700/60 sticky top-0 bg-gray-900 rounded-t-3xl sm:rounded-t-2xl">
                <div>
                    <h3 class="text-lg font-bold text-white">
                        {{ $editMode ? 'রাউটার এডিট' : 'নতুন রাউটার যোগ' }}
                    </h3>
                    <p class="text-xs text-gray-500 mt-0.5">MikroTik API সংযোগ তথ্য</p>
                </div>
                <button wire:click="closeModal" class="p-2 text-gray-400 hover:text-white hover:bg-gray-700 rounded-xl transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <form wire:submit="save" class="p-6 space-y-4">
                <!-- Router Name -->
                <div>
                    <label class="block text-xs font-semibold text-gray-400 mb-1.5 uppercase tracking-wider">রাউটার নাম *</label>
                    <input type="text" wire:model="name" placeholder="My Hotspot"
                           class="w-full bg-gray-800 border border-gray-700 text-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500/50 transition">
                    @error('name') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- IP + Port -->
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-gray-400 mb-1.5 uppercase tracking-wider">IP অ্যাড্রেস *</label>
                        <input type="text" wire:model="ip_address" placeholder="192.168.88.1"
                               class="w-full bg-gray-800 border border-gray-700 text-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500/50 transition">
                        @error('ip_address') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-400 mb-1.5 uppercase tracking-wider">API পোর্ট *</label>
                        <input type="number" wire:model="port" placeholder="8728"
                               class="w-full bg-gray-800 border border-gray-700 text-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500/50 transition">
                        @error('port') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <!-- Username + Password -->
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-gray-400 mb-1.5 uppercase tracking-wider">ইউজারনেম *</label>
                        <input type="text" wire:model="username" placeholder="admin"
                               class="w-full bg-gray-800 border border-gray-700 text-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500/50 transition">
                        @error('username') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-400 mb-1.5 uppercase tracking-wider">পাসওয়ার্ড {{ $editMode ? '' : '*' }}</label>
                        <input type="password" wire:model="password" placeholder="{{ $editMode ? '(অপরিবর্তিত)' : '••••••••' }}"
                               class="w-full bg-gray-800 border border-gray-700 text-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500/50 transition">
                        @error('password') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <!-- Hotspot Name + DNS -->
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-gray-400 mb-1.5 uppercase tracking-wider">হটস্পট নাম</label>
                        <input type="text" wire:model="hotspot_name" placeholder="hotspot1"
                               class="w-full bg-gray-800 border border-gray-700 text-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-indigo-500 transition">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-400 mb-1.5 uppercase tracking-wider">DNS নাম</label>
                        <input type="text" wire:model="dns_name" placeholder="wifi.skynity.com"
                               class="w-full bg-gray-800 border border-gray-700 text-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-indigo-500 transition">
                    </div>
                </div>

                <!-- Hotspot URL -->
                <div>
                    <label class="block text-xs font-semibold text-gray-400 mb-1.5 uppercase tracking-wider">হটস্পট Login URL</label>
                    <input type="url" wire:model="hotspot_url" placeholder="http://192.168.88.1/login"
                           class="w-full bg-gray-800 border border-gray-700 text-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-indigo-500 transition">
                    <p class="text-xs text-gray-600 mt-1">MikroTik হটস্পট লগইন পেজের URL</p>
                    @error('hotspot_url') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Test Result -->
                @if($testResult)
                <div class="p-3.5 rounded-xl text-sm font-medium flex items-center gap-2
                    {{ $testResult === 'success' ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/30' : 'bg-red-500/10 text-red-400 border border-red-500/30' }}">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        @if($testResult === 'success')
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        @else
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        @endif
                    </svg>
                    {{ $testResult === 'success' ? 'কানেকশন সফল!' : 'কানেকশন ব্যর্থ! IP/পোর্ট/পাসওয়ার্ড চেক করুন।' }}
                </div>
                @endif

                <!-- Buttons -->
                <div class="flex gap-3 pt-2">
                    <button type="button" wire:click="testConnection"
                            class="flex-1 py-3 bg-gray-700 hover:bg-gray-600 text-gray-300 text-sm font-semibold rounded-xl transition border border-gray-600">
                        কানেকশন টেস্ট
                    </button>
                    <button type="submit"
                            class="flex-1 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-500 hover:to-purple-500 text-white text-sm font-semibold rounded-xl transition shadow-lg shadow-indigo-500/20">
                        {{ $editMode ? 'আপডেট করুন' : 'সেভ করুন' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>
