<div>
    <!-- Welcome Header -->
    <div class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-2xl p-6 md:p-8 mb-8 text-white">
        <h1 class="text-2xl md:text-3xl font-bold mb-2">স্বাগতম, {{ $user->name }}!</h1>
        <p class="opacity-90">আপনার ইন্টারনেট সংযোগ সম্পর্কে সব তথ্য এখানে দেখুন।</p>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Subscription Status -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-medium text-gray-500">সাবস্ক্রিপশন</h3>
                @if($user->hasActiveSubscription())
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">অ্যাক্টিভ</span>
                @else
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">মেয়াদোত্তীর্ণ</span>
                @endif
            </div>
            <p class="text-2xl font-bold text-gray-900">{{ $package->name ?? 'N/A' }}</p>
            <p class="text-sm text-gray-500 mt-1">{{ $router->name ?? 'N/A' }}</p>
        </div>

        <!-- Days Remaining -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="text-sm font-medium text-gray-500 mb-4">বাকি দিন</h3>
            <p class="text-4xl font-bold {{ $user->daysRemaining() <= 3 ? 'text-red-600' : 'text-indigo-600' }}">
                {{ $user->daysRemaining() }}
            </p>
            <p class="text-sm text-gray-500 mt-1">
                মেয়াদ: {{ $user->subscription_expires_at?->format('d M Y') ?? 'N/A' }}
            </p>
        </div>

        <!-- Usage Stats -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="text-sm font-medium text-gray-500 mb-4">ব্যবহার</h3>
            <p class="text-2xl font-bold text-gray-900">{{ $usageStats['total_data'] }}</p>
            <p class="text-sm text-gray-500 mt-1">মোট ডাটা ব্যবহার</p>
        </div>

        <!-- Last Login -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="text-sm font-medium text-gray-500 mb-4">সর্বশেষ লগইন</h3>
            <p class="text-lg font-medium text-gray-900">{{ $usageStats['last_login'] }}</p>
        </div>
    </div>

    <!-- Connection Credentials -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-lg font-semibold text-gray-900">লগইন তথ্য</h2>
                <button wire:click="toggleCredentials" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">
                    {{ $showCredentials ? 'লুকান' : 'দেখান' }}
                </button>
            </div>

            <div class="space-y-4">
                <div class="bg-gray-50 rounded-lg p-4">
                    <p class="text-sm text-gray-500 mb-1">ইউজারনেম</p>
                    <p class="text-xl font-mono font-bold text-gray-900">{{ $user->hotspot_username ?? 'N/A' }}</p>
                </div>
                <div class="bg-gray-50 rounded-lg p-4">
                    <p class="text-sm text-gray-500 mb-1">পাসওয়ার্ড</p>
                    <p class="text-xl font-mono font-bold text-gray-900">
                        {{ $showCredentials ? ($user->hotspot_password ?? '********') : '••••••••' }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Package Details -->
        @if($package)
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-6">প্যাকেজ বিবরণ</h2>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-gray-500">প্যাকেজ নাম</span>
                    <span class="font-medium">{{ $package->name }}</span>
                </div>
                @if($package->speed_limit)
                <div class="flex justify-between">
                    <span class="text-gray-500">স্পিড</span>
                    <span class="font-medium">{{ $package->speed_limit }}</span>
                </div>
                @endif
                @if($package->data_limit)
                <div class="flex justify-between">
                    <span class="text-gray-500">ডাটা লিমিট</span>
                    <span class="font-medium">{{ $package->data_limit }}</span>
                </div>
                @endif
                <div class="flex justify-between">
                    <span class="text-gray-500">মূল্য</span>
                    <span class="font-medium">৳{{ number_format($package->price) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">মেয়াদ</span>
                    <span class="font-medium">{{ $package->validity_days }} দিন</span>
                </div>
            </div>

            @if($user->daysRemaining() <= 5)
            <div class="mt-6">
                <a href="{{ route('captive.index') }}" class="block w-full bg-indigo-600 hover:bg-indigo-700 text-white text-center py-3 rounded-lg font-medium transition">
                    রিনিউ করুন
                </a>
            </div>
            @endif
        </div>
        @endif
    </div>

    <!-- Notifications -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b flex items-center justify-between">
            <h2 class="text-lg font-semibold text-gray-900">নোটিফিকেশন</h2>
            @if($notifications->where('is_read', false)->count() > 0)
            <button wire:click="markAllAsRead" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">
                সব পড়া হয়েছে চিহ্নিত করুন
            </button>
            @endif
        </div>
        <div class="divide-y">
            @forelse($notifications as $notification)
            <div class="px-6 py-4 {{ $notification->is_read ? 'bg-white' : 'bg-blue-50' }} hover:bg-gray-50 transition cursor-pointer" wire:click="markAsRead({{ $notification->id }})">
                <div class="flex items-start space-x-3">
                    <div class="flex-shrink-0">
                        @if($notification->type === 'approval')
                        <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        @else
                        <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                            </svg>
                        </div>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-medium text-gray-900">{{ $notification->title }}</p>
                        <p class="text-sm text-gray-600 whitespace-pre-line">{{ $notification->message }}</p>
                        <p class="text-xs text-gray-400 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                    </div>
                    @if(!$notification->is_read)
                    <div class="flex-shrink-0">
                        <span class="w-2 h-2 bg-blue-600 rounded-full inline-block"></span>
                    </div>
                    @endif
                </div>
            </div>
            @empty
            <div class="px-6 py-12 text-center text-gray-500">
                কোন নোটিফিকেশন নেই
            </div>
            @endforelse
        </div>
    </div>
</div>
