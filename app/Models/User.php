<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'role',
        'hotspot_username',
        'hotspot_password',
        'router_id',
        'package_id',
        'subscription_expires_at',
        'mac_address',
        'is_active',
        'balance',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'hotspot_password',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'subscription_expires_at' => 'datetime',
            'balance' => 'decimal:2',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isOperator(): bool
    {
        return $this->role === 'operator';
    }

    public function isCustomer(): bool
    {
        return $this->role === 'customer';
    }

    public function router()
    {
        return $this->belongsTo(Router::class);
    }

    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    public function vouchers()
    {
        return $this->hasMany(Voucher::class, 'created_by');
    }

    public function sales()
    {
        return $this->hasMany(Sale::class, 'sold_by');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function hotspotRequests()
    {
        return $this->hasMany(HotspotRequest::class);
    }

    public function unreadNotifications()
    {
        return $this->notifications()->unread();
    }

    public function hasActiveSubscription(): bool
    {
        return $this->subscription_expires_at && $this->subscription_expires_at->isFuture();
    }

    public function daysRemaining(): int
    {
        if (!$this->subscription_expires_at) return 0;
        return max(0, now()->diffInDays($this->subscription_expires_at, false));
    }
}
