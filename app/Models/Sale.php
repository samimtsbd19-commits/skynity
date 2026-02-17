<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'voucher_id',
        'sold_by',
        'amount',
        'payment_method',
        'note',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function voucher()
    {
        return $this->belongsTo(Voucher::class);
    }

    public function seller()
    {
        return $this->belongsTo(User::class, 'sold_by');
    }

    /**
     * পেমেন্ট মেথড লেবেল পান
     */
    public function getPaymentMethodLabelAttribute(): string
    {
        return match($this->payment_method) {
            'cash' => 'নগদ',
            'bkash' => 'বিকাশ',
            'nagad' => 'নগদ',
            'rocket' => 'রকেট',
            default => $this->payment_method,
        };
    }
}
