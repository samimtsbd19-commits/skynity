<div>
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 space-y-4 md:space-y-0">
        <div>
            <h1 class="text-2xl font-bold bg-gradient-to-r from-blue-400 to-cyan-500 bg-clip-text text-transparent">
                📦 Package Management
            </h1>
            <p class="text-gray-400 text-sm mt-1">View and manage all packages</p>
        </div>
        <button wire:click="openModal" 
                class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-500 to-cyan-600 text-white font-semibold rounded-xl hover:from-blue-600 hover:to-cyan-700 transition shadow-lg shadow-blue-500/30">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            New Package
        </button>
    </div>

    <!-- Filter by Router -->
    <div class="bg-gray-800/50 backdrop-blur border border-gray-700/50 rounded-2xl p-4 mb-6">
        <div class="flex items-center space-x-4">
            <label class="text-gray-300 font-medium">Router:</label>
            <select wire:model.live="selectedRouter" 
                    class="px-4 py-2 bg-gray-700/50 border border-gray-600 rounded-xl text-white focus:outline-none focus:border-blue-500">
                <option value="">All Routers</option>
                @foreach($routers as $router)
                <option value="{{ $router->id }}">{{ $router->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <!-- Packages Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($packages as $package)
        <div class="bg-gradient-to-br from-gray-800/80 to-gray-900/80 backdrop-blur border border-gray-700/50 rounded-2xl overflow-hidden hover:border-blue-500/50 hover:shadow-lg hover:shadow-blue-500/10 transition-all">
            <div class="p-5">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h3 class="text-lg font-semibold text-white">{{ $package->name }}</h3>
                        <p class="text-sm text-gray-400">{{ $package->router->name ?? 'N/A' }}</p>
                    </div>
                    <span class="px-4 py-2 bg-gradient-to-r from-blue-500 to-cyan-600 text-white rounded-xl font-bold shadow-lg">
                        ৳{{ number_format($package->price) }}
                    </span>
                </div>
                
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between items-center p-2 bg-gray-700/30 rounded-lg">
                        <span class="text-gray-400">Profile:</span>
                        <span class="font-medium text-cyan-400">{{ $package->mikrotik_profile }}</span>
                    </div>
                    <div class="flex justify-between items-center p-2 bg-gray-700/30 rounded-lg">
                        <span class="text-gray-400">Validity:</span>
                        <span class="font-medium text-white">{{ $package->validity ? $package->validity . ' days' : 'Unlimited' }}</span>
                    </div>
                    @if($package->speed_limit)
                    <div class="flex justify-between items-center p-2 bg-gray-700/30 rounded-lg">
                        <span class="text-gray-400">Speed:</span>
                        <span class="font-medium text-emerald-400">{{ $package->speed_limit }}</span>
                    </div>
                    @endif
                </div>
                
                <div class="flex items-center justify-end space-x-2 mt-4 pt-4 border-t border-gray-700/50">
                    <button wire:click="editPackage({{ $package->id }})"
                            class="p-2 text-blue-400 hover:text-blue-300 hover:bg-blue-500/20 rounded-lg transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                    </button>
                    <button wire:click="deletePackage({{ $package->id }})" wire:confirm="এই প্যাকেজ মুছে ফেলতে চান?"
                            class="p-2 text-red-400 hover:text-red-300 hover:bg-red-500/20 rounded-lg transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full bg-gray-800/50 rounded-2xl p-12 text-center">
            <svg class="w-20 h-20 mx-auto mb-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
            </svg>
            <p class="text-gray-400 text-lg">No packages found</p>
            <button wire:click="openModal" class="mt-4 text-blue-400 hover:text-blue-300">
                Create a new package →
            </button>
        </div>
        @endforelse
    </div>

    <!-- Modal -->
    @if($showModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-sm">
        <div class="bg-gray-800 border border-gray-700 rounded-2xl w-full max-w-lg mx-4 p-6 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold text-white">
                    {{ $editMode ? '✏️ Edit Package' : '➕ New Package' }}
                </h3>
                <button wire:click="closeModal" class="text-gray-400 hover:text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            
            <form wire:submit="{{ $editMode ? 'updatePackage' : 'createPackage' }}" class="space-y-4">
                <div>
                    <label class="block text-sm text-gray-300 mb-2">Router *</label>
                    <select wire:model="router_id" required
                            class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-xl text-white focus:outline-none focus:border-blue-500">
                        <option value="">Select a router</option>
                        @foreach($routers as $router)
                        <option value="{{ $router->id }}">{{ $router->name }}</option>
                        @endforeach
                    </select>
                    @error('router_id') <span class="text-red-400 text-sm">{{ $message }}</span> @enderror
                </div>
                
                <div>
                    <label class="block text-sm text-gray-300 mb-2">Package name *</label>
                    <input type="text" wire:model="name" required placeholder="e.g., 10 Mbps - 30 Days"
                           class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-xl text-white placeholder-gray-400 focus:outline-none focus:border-blue-500">
                    @error('name') <span class="text-red-400 text-sm">{{ $message }}</span> @enderror
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm text-gray-300 mb-2">MikroTik profile *</label>
                        <input type="text" wire:model="mikrotik_profile" required placeholder="default"
                               class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-xl text-white placeholder-gray-400 focus:outline-none focus:border-blue-500">
                        @error('mikrotik_profile') <span class="text-red-400 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm text-gray-300 mb-2">Validity (days)</label>
                        <input type="number" wire:model="validity" placeholder="30"
                               class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-xl text-white placeholder-gray-400 focus:outline-none focus:border-blue-500">
                    </div>
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm text-gray-300 mb-2">Price (৳) *</label>
                        <input type="number" wire:model="price" required placeholder="100"
                               class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-xl text-white placeholder-gray-400 focus:outline-none focus:border-blue-500">
                        @error('price') <span class="text-red-400 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm text-gray-300 mb-2">Selling price (৳)</label>
                        <input type="number" wire:model="selling_price" placeholder="150"
                               class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-xl text-white placeholder-gray-400 focus:outline-none focus:border-blue-500">
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm text-gray-300 mb-2">Speed limit</label>
                    <input type="text" wire:model="speed_limit" placeholder="10M/10M"
                           class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-xl text-white placeholder-gray-400 focus:outline-none focus:border-blue-500">
                </div>
                
                <div class="flex items-center space-x-2">
                    <input type="checkbox" wire:model="is_active" id="is_active"
                           class="rounded border-gray-600 bg-gray-700 text-blue-600 focus:ring-blue-500">
                    <label for="is_active" class="text-gray-300">Active</label>
                </div>
                
                <div class="flex gap-3 pt-4">
                    <button type="button" wire:click="closeModal"
                            class="flex-1 px-4 py-3 bg-gray-700 text-gray-300 rounded-xl hover:bg-gray-600 transition">
                        Cancel
                    </button>
                    <button type="submit"
                            class="flex-1 px-4 py-3 bg-gradient-to-r from-blue-500 to-cyan-600 text-white font-semibold rounded-xl hover:from-blue-600 hover:to-cyan-700 transition">
                        {{ $editMode ? 'Update' : 'Create' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>
