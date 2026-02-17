<?php

namespace App\Livewire;

use App\Models\Notification;
use Livewire\Component;

class AdminNotifications extends Component
{
    public $notifications = [];
    public $unreadCount = 0;
    public $showDropdown = false;
    public $lastUnreadCount = 0;

    protected $listeners = ['refreshNotifications' => 'loadNotifications'];

    public function mount()
    {
        $this->loadNotifications();
    }

    public function loadNotifications()
    {
        $prev = $this->unreadCount;
        $this->notifications = Notification::where('user_id', auth()->id())
            ->latest()
            ->take(10)
            ->get();

        $this->unreadCount = Notification::where('user_id', auth()->id())
            ->where('is_read', false)
            ->count();
        
        if ($this->unreadCount > $prev) {
            $this->dispatchBrowserEvent('notification-sound');
        }
    }

    public function toggleDropdown()
    {
        $this->showDropdown = !$this->showDropdown;
    }

    public function markAsRead($id)
    {
        $notification = Notification::find($id);
        if ($notification && $notification->user_id === auth()->id()) {
            $notification->markAsRead();
            $this->loadNotifications();
        }
    }

    public function markAllAsRead()
    {
        Notification::where('user_id', auth()->id())
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
        $this->loadNotifications();
    }

    public function viewRequest($notificationId)
    {
        $notification = Notification::find($notificationId);
        if ($notification && isset($notification->data['request_id'])) {
            $this->markAsRead($notificationId);
            return redirect()->route('hotspot.requests', ['view' => $notification->data['request_id']]);
        }
    }

    public function render()
    {
        return view('livewire.admin-notifications');
    }
}
