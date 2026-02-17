<div>
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 space-y-4 md:space-y-0">
        <h1 class="text-2xl font-bold bg-gradient-to-r from-indigo-400 to-purple-500 bg-clip-text text-transparent">ক্যাপটিভ পোর্টাল টেমপ্লেট</h1>
        <button wire:click="openCreateModal" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
            <span>নতুন টেমপ্লেট</span>
        </button>
    </div>

    <!-- Flash Messages -->
    @if (session()->has('success'))
    <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-6">
        {{ session('success') }}
    </div>
    @endif
    @if (session()->has('error'))
    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6">
        {{ session('error') }}
    </div>
    @endif

    <!-- Template Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($templates as $template)
        <div class="bg-white rounded-xl shadow-sm overflow-hidden border {{ $template->is_default ? 'border-indigo-500 ring-2 ring-indigo-200' : 'border-gray-200' }}">
            <!-- Preview Header -->
            <div class="h-32 relative" style="background: {{ $template->background_color }}">
                @if($template->logo_path)
                <img src="{{ asset('storage/' . $template->logo_path) }}" class="absolute inset-0 m-auto h-16 object-contain" alt="">
                @else
                <div class="absolute inset-0 flex items-center justify-center text-white text-xl font-bold opacity-75">
                    {{ $template->name }}
                </div>
                @endif
                @if($template->is_default)
                <span class="absolute top-2 right-2 bg-indigo-600 text-white text-xs px-2 py-1 rounded-full">ডিফল্ট</span>
                @endif
            </div>

            <!-- Template Info -->
            <div class="p-4">
                <h3 class="font-semibold text-gray-900 mb-1">{{ $template->name }}</h3>
                <p class="text-sm text-gray-500 mb-3">
                    {{ $template->router ? $template->router->name : 'সব রাউটারের জন্য' }}
                </p>

                <!-- Color Swatches -->
                <div class="flex items-center space-x-2 mb-4">
                    <div class="w-6 h-6 rounded-full border" style="background: {{ $template->background_color }}" title="Background"></div>
                    <div class="w-6 h-6 rounded-full border" style="background: {{ $template->primary_color }}" title="Primary"></div>
                    <div class="w-6 h-6 rounded-full border" style="background: {{ $template->text_color }}" title="Text"></div>
                </div>

                <!-- Actions -->
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-2">
                        <button wire:click="editTemplate({{ $template->id }})" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">
                            এডিট
                        </button>
                        <a href="{{ route('captive.preview', ['template' => $template->id]) }}" target="_blank" class="text-gray-600 hover:text-gray-800 text-sm font-medium">
                            প্রিভিউ
                        </a>
                    </div>
                    <div class="flex items-center space-x-2">
                        @if(!$template->is_default)
                        <button wire:click="setDefault({{ $template->id }})" class="text-gray-500 hover:text-indigo-600 text-sm">
                            ডিফল্ট করুন
                        </button>
                        <button wire:click="deleteTemplate({{ $template->id }})" wire:confirm="এই টেমপ্লেট ডিলিট করতে চান?" class="text-red-500 hover:text-red-700 text-sm">
                            ডিলিট
                        </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full text-center py-12 bg-white rounded-xl">
            <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"/>
            </svg>
            <p class="text-gray-500">কোন টেমপ্লেট নেই</p>
            <button wire:click="openCreateModal" class="mt-4 text-indigo-600 hover:text-indigo-800 font-medium">
                প্রথম টেমপ্লেট তৈরি করুন
            </button>
        </div>
        @endforelse
    </div>

    <!-- Create/Edit Modal -->
    @if($showModal)
    <div class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-start justify-center min-h-screen px-4 py-8">
            <div class="fixed inset-0 bg-black/50" wire:click="closeModal"></div>
            <div class="relative bg-white rounded-xl shadow-xl max-w-4xl w-full p-6 max-h-[90vh] overflow-y-auto">
                <h3 class="text-lg font-bold text-gray-900 mb-6">
                    {{ $isEditing ? 'টেমপ্লেট এডিট করুন' : 'নতুন টেমপ্লেট তৈরি করুন' }}
                </h3>

                <form wire:submit="saveTemplate">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Basic Info -->
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">টেমপ্লেট নাম *</label>
                                <input type="text" wire:model="name" class="w-full border border-gray-300 rounded-lg px-3 py-2 bg-white text-gray-900 focus:ring-2 focus:ring-indigo-500" required>
                                @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">রাউটার</label>
                                <select wire:model="router_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 bg-white text-gray-900">
                                    <option value="">সব রাউটার</option>
                                    @foreach($routers as $router)
                                    <option value="{{ $router->id }}">{{ $router->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">স্বাগত শিরোনাম</label>
                                <input type="text" wire:model="welcome_title" class="w-full border border-gray-300 rounded-lg px-3 py-2 bg-white text-gray-900">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">স্বাগত বার্তা</label>
                                <textarea wire:model="welcome_message" rows="2" class="w-full border border-gray-300 rounded-lg px-3 py-2 bg-white text-gray-900"></textarea>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">ফুটার টেক্সট</label>
                                <input type="text" wire:model="footer_text" class="w-full border border-gray-300 rounded-lg px-3 py-2 bg-white text-gray-900">
                            </div>
                        </div>

                        <!-- Colors & Settings -->
                        <div class="space-y-4">
                            <div class="grid grid-cols-3 gap-3">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">ব্যাকগ্রাউন্ড</label>
                                    <input type="color" wire:model="background_color" class="w-full h-10 rounded-lg cursor-pointer">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">প্রাইমারি</label>
                                    <input type="color" wire:model="primary_color" class="w-full h-10 rounded-lg cursor-pointer">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">টেক্সট</label>
                                    <input type="color" wire:model="text_color" class="w-full h-10 rounded-lg cursor-pointer">
                                </div>
                            </div>

                            <!-- Typography -->
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">ফন্ট</label>
                                    <select wire:model="font_family" class="w-full border border-gray-300 rounded-lg px-3 py-2 bg-white text-gray-900">
                                        <option value="Poppins">Poppins</option>
                                        <option value="Inter">Inter</option>
                                        <option value="Roboto">Roboto</option>
                                        <option value="Noto Sans Bengali">Noto Sans Bengali</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">বেস ফন্ট সাইজ (px)</label>
                                    <input type="number" min="12" max="20" wire:model="base_font_size" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">হেডিং সাইজ (px)</label>
                                    <input type="number" min="18" max="36" wire:model="heading_font_size" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                                </div>
                            </div>

                            <!-- Package Card -->
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">কার্ড রেডিয়াস (px)</label>
                                    <input type="number" min="8" max="32" wire:model="package_card_radius" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">কার্ড শ্যাডো</label>
                                    <label class="flex items-center">
                                        <input type="checkbox" wire:model="package_card_shadow" class="rounded border-gray-300 text-indigo-600">
                                        <span class="ml-2 text-sm text-gray-700">অন</span>
                                    </label>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">কার্ড ব্যাকগ্রাউন্ড</label>
                                    <input type="color" wire:model="package_card_bg" class="w-full h-10 rounded-lg cursor-pointer">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">কার্ড টেক্সট</label>
                                    <input type="color" wire:model="package_card_text" class="w-full h-10 rounded-lg cursor-pointer">
                                </div>
                            </div>

                            <!-- Grid Columns -->
                            <div class="grid grid-cols-3 gap-3">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">কলাম (মোবাইল)</label>
                                    <input type="number" min="1" max="3" wire:model="package_grid_sm" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">কলাম (ট্যাবলেট)</label>
                                    <input type="number" min="1" max="3" wire:model="package_grid_md" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">কলাম (ডেস্কটপ)</label>
                                    <input type="number" min="1" max="4" wire:model="package_grid_lg" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                                </div>
                            </div>

                            <!-- CTA Button -->
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">CTA বাটন টেক্সট</label>
                                    <input type="text" wire:model="cta_button_text" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">CTA রেডিয়াস (px)</label>
                                    <input type="number" min="6" max="32" wire:model="button_radius" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">CTA ব্যাকগ্রাউন্ড</label>
                                    <input type="color" wire:model="cta_button_color" class="w-full h-10 rounded-lg cursor-pointer">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">CTA টেক্সট রঙ</label>
                                    <input type="color" wire:model="cta_button_text_color" class="w-full h-10 rounded-lg cursor-pointer">
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">লোগো</label>
                                <input type="file" wire:model="logo" accept="image/*" class="w-full border border-gray-300 rounded-lg px-3 py-2 bg-white text-gray-900">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">ব্যাকগ্রাউন্ড ইমেজ</label>
                                <input type="file" wire:model="background_image" accept="image/*" class="w-full border border-gray-300 rounded-lg px-3 py-2 bg-white text-gray-900">
                            </div>

                            <div class="space-y-2">
                                <label class="flex items-center">
                                    <input type="checkbox" wire:model="require_phone" class="rounded border-gray-300 text-indigo-600">
                                    <span class="ml-2 text-sm text-gray-700">ফোন নম্বর আবশ্যক</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" wire:model="require_email" class="rounded border-gray-300 text-indigo-600">
                                    <span class="ml-2 text-sm text-gray-700">ইমেইল আবশ্যক</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" wire:model="show_packages" class="rounded border-gray-300 text-indigo-600">
                                    <span class="ml-2 text-sm text-gray-700">প্যাকেজ দেখান</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" wire:model="is_active" class="rounded border-gray-300 text-indigo-600">
                                    <span class="ml-2 text-sm text-gray-700">অ্যাক্টিভ</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" wire:model="is_default" class="rounded border-gray-300 text-indigo-600">
                                    <span class="ml-2 text-sm text-gray-700">ডিফল্ট টেমপ্লেট</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Terms & Custom Code -->
                    <div class="mt-6 space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">শর্তাবলী</label>
                            <textarea wire:model="terms_conditions" rows="3" class="w-full border border-gray-300 rounded-lg px-3 py-2 bg-white text-gray-900"></textarea>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">কাস্টম CSS</label>
                                <textarea wire:model="custom_css" rows="3" class="w-full border border-gray-300 rounded-lg px-3 py-2 bg-white text-gray-900 font-mono text-sm" placeholder=".my-class { }"></textarea>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">কাস্টম JavaScript</label>
                                <textarea wire:model="custom_js" rows="3" class="w-full border border-gray-300 rounded-lg px-3 py-2 bg-white text-gray-900 font-mono text-sm" placeholder="console.log('Hello');"></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex justify-end space-x-3 mt-6 pt-6 border-t">
                        <button type="button" wire:click="closeModal" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                            বাতিল
                        </button>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                            {{ $isEditing ? 'আপডেট করুন' : 'তৈরি করুন' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
</div>
