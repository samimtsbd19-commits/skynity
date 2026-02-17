<div>
    @section('title', 'Generate Vouchers')
    
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Generate Form -->
        <div class="bg-gray-800 rounded-xl border border-gray-700 p-6">
            <h3 class="text-lg font-semibold text-gray-200 mb-6 flex items-center">
                <svg class="w-5 h-5 mr-2 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Generate New Vouchers
            </h3>
            
            <form wire:submit="generate" class="space-y-4">
                <!-- Router -->
                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-2">Select Router *</label>
                    <select wire:model.live="selectedRouter" class="w-full bg-gray-700 border border-gray-600 text-gray-200 rounded-lg px-4 py-3 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">-- Select a router --</option>
                        @foreach($routers as $router)
                            <option value="{{ $router->id }}">{{ $router->name }}</option>
                        @endforeach
                    </select>
                    @error('selectedRouter') <span class="text-red-400 text-sm">{{ $message }}</span> @enderror
                </div>
                
                <!-- Package -->
                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-2">Select Package *</label>
                    <select wire:model="selectedPackage" class="w-full bg-gray-700 border border-gray-600 text-gray-200 rounded-lg px-4 py-3 focus:ring-indigo-500 focus:border-indigo-500" {{ empty($packages) ? 'disabled' : '' }}>
                        <option value="">-- Select a package --</option>
                        @foreach($packages as $package)
                            <option value="{{ $package->id }}">{{ $package->name }} - ৳{{ $package->selling_price }}</option>
                        @endforeach
                    </select>
                    @error('selectedPackage') <span class="text-red-400 text-sm">{{ $message }}</span> @enderror
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <!-- Quantity -->
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-2">Quantity *</label>
                        <input type="number" wire:model="quantity" min="1" max="100" class="w-full bg-gray-700 border border-gray-600 text-gray-200 rounded-lg px-4 py-3 focus:ring-indigo-500 focus:border-indigo-500">
                        @error('quantity') <span class="text-red-400 text-sm">{{ $message }}</span> @enderror
                    </div>
                    
                    <!-- Prefix -->
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-2">Prefix</label>
                        <input type="text" wire:model="prefix" placeholder="SKY-" class="w-full bg-gray-700 border border-gray-600 text-gray-200 rounded-lg px-4 py-3 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <!-- Username Length -->
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-2">Username Length</label>
                        <select wire:model="usernameLength" class="w-full bg-gray-700 border border-gray-600 text-gray-200 rounded-lg px-4 py-3 focus:ring-indigo-500 focus:border-indigo-500">
                            @for($i = 4; $i <= 16; $i++)
                                <option value="{{ $i }}">{{ $i }} chars</option>
                            @endfor
                        </select>
                    </div>
                    
                    <!-- User Mode -->
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-2">Mode</label>
                        <select wire:model="userMode" class="w-full bg-gray-700 border border-gray-600 text-gray-200 rounded-lg px-4 py-3 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="voucher">Voucher code (same)</option>
                            <option value="user_pass">Username & Password (separate)</option>
                        </select>
                    </div>
                </div>
                
                <button type="submit" class="w-full bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-semibold py-3 px-6 rounded-lg flex items-center justify-center transition">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Generate
                </button>
            </form>
        </div>
        
        <!-- Generated Vouchers -->
        <div class="bg-gray-800 rounded-xl border border-gray-700 p-6">
            <h3 class="text-lg font-semibold text-gray-200 mb-6 flex items-center">
                <svg class="w-5 h-5 mr-2 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Generated Vouchers
            </h3>
            
            @if($showGenerated && count($generatedVouchers) > 0)
                <div class="mb-4 flex space-x-2">
                    <button onclick="window.print()" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg flex items-center transition">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                        </svg>
                        Print
                    </button>
                    <button wire:click="resetForm" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg flex items-center transition">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        Reset
                    </button>
                </div>
                
                <div class="overflow-x-auto max-h-96">
                    <table class="w-full">
                        <thead class="sticky top-0 bg-gray-800">
                            <tr class="text-left text-gray-400 text-sm border-b border-gray-700">
                                <th class="pb-3">#</th>
                                <th class="pb-3">Username</th>
                                <th class="pb-3">Password</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm">
                            @foreach($generatedVouchers as $index => $voucher)
                                <tr class="border-b border-gray-700/50 hover:bg-gray-700/30">
                                    <td class="py-2 text-gray-500">{{ $index + 1 }}</td>
                                    <td class="py-2 text-indigo-400 font-mono">{{ $voucher->username }}</td>
                                    <td class="py-2 text-gray-200 font-mono">{{ $voucher->password }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-12 text-gray-500">
                    <svg class="w-16 h-16 mx-auto mb-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
                    </svg>
                    <p>Fill the form to generate vouchers</p>
                </div>
            @endif
        </div>
    </div>
</div>
