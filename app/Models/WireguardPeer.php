<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WireguardPeer extends Model
{
    use HasFactory;

    protected $fillable = [
        'router_id',
        'user_id',
        'interface',
        'public_key',
        'preshared_key',
        'allowed_address',
        'comment',
        'persistent_keepalive',
        'status',
    ];

    public function router()
    {
        return $this->belongsTo(Router::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
