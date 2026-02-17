<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    use HasFactory;

    protected $fillable = [
        'router_id',
        'name',
        'mikrotik_profile',
        'validity',
        'data_limit',
        'speed_limit',
        'price',
        'selling_price',
        'description',
        'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'selling_price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function router()
    {
        return $this->belongsTo(Router::class);
    }

    public function vouchers()
    {
        return $this->hasMany(Voucher::class);
    }

    /**
     * ভ্যালিডিটি ফরম্যাট করুন
     */
    public function getFormattedValidityAttribute(): string
    {
        $validity = $this->validity;
        
        if (!$validity) return 'Unlimited';
        
        // Parse validity like 1h, 1d, 7d, 30d
        preg_match('/(\d+)([hdwm])/', $validity, $matches);
        
        if (count($matches) !== 3) return $validity;
        
        $number = $matches[1];
        $unit = $matches[2];
        
        $units = [
            'h' => 'ঘন্টা',
            'd' => 'দিন',
            'w' => 'সপ্তাহ',
            'm' => 'মাস',
        ];
        
        return $number . ' ' . ($units[$unit] ?? $unit);
    }

    /**
     * ডাটা লিমিট ফরম্যাট করুন
     */
    public function getFormattedDataLimitAttribute(): string
    {
        if (!$this->data_limit || $this->data_limit === 'unlimited') {
            return 'Unlimited';
        }
        
        return $this->data_limit;
    }
}
