<div class="relative" x-data="{ open: false }" wire:poll.10s="loadNotifications" x-init="
    window.addEventListener('notification-sound', () => {
        try {
            const ctx = new (window.AudioContext || window.webkitAudioContext)();
            const o = ctx.createOscillator();
            const g = ctx.createGain();
            o.type = 'sine';
            o.frequency.value = 880;
            o.connect(g);
            g.connect(ctx.destination);
            g.gain.setValueAtTime(0.001, ctx.currentTime);
            g.gain.exponentialRampToValueAtTime(0.2, ctx.currentTime + 0.01);
            o.start();
            setTimeout(() => { g.gain.exponentialRampToValueAtTime(0.0001, ctx.currentTime + 0.2); o.stop(); }, 200);
        } catch (e) {}
    });
">
    <button @click="open = !open" class="relative p-2 text-gray-400 hover:text-white transition">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
        </svg>
        @if($unreadCount > 0)
        <span class="absolute top-0 right-0 inline-flex items-center justify-center w-5 h-5 text-xs font-bold text-white bg-red-500 rounded-full">
            {{ $unreadCount > 9 ? '9+' : $unreadCount }}
        </span>
        @endif
    </button>

    <!-- Dropdown -->
    <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 mt-2 w-80 bg-white rounded-xl shadow-lg border z-50">
        <div class="px-4 py-3 border-b flex items-center justify-between">
            <h3 class="font-semibold text-gray-900">নোটিফিকেশন</h3>
            @if($unreadCount > 0)
            <button wire:click="markAllAsRead" class="text-xs text-indigo-600 hover:text-indigo-800">
                সব পড়া হয়েছে
            </button>
            @endif
        </div>

        <div class="max-h-96 overflow-y-auto">
            @forelse($notifications as $notification)
            <div wire:click="viewRequest({{ $notification->id }})" 
                 class="px-4 py-3 hover:bg-gray-50 cursor-pointer {{ $notification->is_read ? '' : 'bg-blue-50' }} border-b last:border-b-0">
                <div class="flex items-start space-x-3">
                    <div class="flex-shrink-0">
                        @if($notification->type === 'hotspot_request')
                        <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center">
                            <svg class="w-4 h-4 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        @else
                        <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 truncate">{{ $notification->title }}</p>
                        <p class="text-xs text-gray-500 truncate">{{ Str::limit($notification->message, 50) }}</p>
                        <p class="text-xs text-gray-400 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                    </div>
                    @if(!$notification->is_read)
                    <span class="w-2 h-2 bg-blue-600 rounded-full flex-shrink-0"></span>
                    @endif
                </div>
            </div>
            @empty
            <div class="px-4 py-8 text-center text-gray-500 text-sm">
                কোন নোটিফিকেশন নেই
            </div>
            @endforelse
        </div>

        @if($notifications->count() > 0)
        <div class="px-4 py-3 border-t">
            <a href="{{ route('hotspot.requests') }}" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">
                সব দেখুন →
            </a>
        </div>
        @endif
    </div>
</div>
