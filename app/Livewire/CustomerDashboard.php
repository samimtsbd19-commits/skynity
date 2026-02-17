<?php

namespace App\Livewire;

use App\Models\Notification;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.customer')]
class CustomerDashboard extends Component
{
    public $user;
    public $notifications = [];
    public $showCredentials = false;

    public function mount()
    {
        $this->user = auth()->user();
        $this->loadNotifications();
    }

    public function loadNotifications()
    {
        $this->notifications = Notification::where('user_id', $this->user->id)
            ->latest()
            ->take(10)
            ->get();
    }

    public function toggleCredentials()
    {
        $this->showCredentials = !$this->showCredentials;
    }

    public function markAsRead($notificationId)
    {
        $notification = Notification::find($notificationId);
        if ($notification && $notification->user_id === $this->user->id) {
            $notification->markAsRead();
            $this->loadNotifications();
        }
    }

    public function markAllAsRead()
    {
        Notification::where('user_id', $this->user->id)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
        $this->loadNotifications();
    }

    public function getUsageStatsProperty()
    {
        // This would typically come from MikroTik API
        // For now, return placeholder data
        return [
            'total_data' => '0 MB',
            'session_time' => '0 min',
            'last_login' => $this->user->updated_at?->format('d M Y, h:i A') ?? 'N/A',
        ];
    }

    public function render()
    {
        return view('livewire.customer-dashboard', [
            'package' => $this->user->package,
            'router' => $this->user->router,
            'usageStats' => $this->usageStats,
        ]);
    }
}
