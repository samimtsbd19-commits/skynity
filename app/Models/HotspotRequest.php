<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HotspotRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'router_id',
        'package_id',
        'user_id',
        'customer_name',
        'customer_phone',
        'customer_email',
        'mac_address',
        'ip_address',
        'payment_method',
        'transaction_id',
        'amount',
        'status',
        'approved_by',
        'approved_at',
        'voucher_code',
        'rejection_reason',
        'notes',
        // Trial fields
        'is_trial',
        'trial_days',
        'trial_speed',
        // Custom package fields
        'is_custom',
        'custom_speed',
        'custom_days',
        'custom_devices',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'approved_at' => 'datetime',
        'is_trial' => 'boolean',
        'is_custom' => 'boolean',
        'trial_days' => 'integer',
        'custom_days' => 'integer',
        'custom_devices' => 'integer',
    ];

    public function router()
    {
        return $this->belongsTo(Router::class);
    }

    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }
}
