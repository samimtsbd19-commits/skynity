<?php

namespace App\Models;

use App\Services\MikrotikApi;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class Router extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'ip_address',
        'port',
        'username',
        'password',
        'hotspot_name',
        'dns_name',
        'hotspot_url',
        'wireguard_interface',
        'wireguard_endpoint',
        'wireguard_server_public_key',
        'wireguard_subnet',
        'wireguard_dns',
        'wireguard_keepalive',
        'is_active',
        'last_connected_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'last_connected_at' => 'datetime',
    ];

    // পাসওয়ার্ড এনক্রিপ্ট করে সেভ করুন
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Crypt::encryptString($value);
    }

    // পাসওয়ার্ড ডিক্রিপ্ট করে পান
    public function getDecryptedPasswordAttribute(): string
    {
        return Crypt::decryptString($this->password);
    }

    public function packages()
    {
        return $this->hasMany(Package::class);
    }

    public function vouchers()
    {
        return $this->hasMany(Voucher::class);
    }

    public function sessionLogs()
    {
        return $this->hasMany(SessionLog::class);
    }

    public function wireguardPeers()
    {
        return $this->hasMany(WireguardPeer::class);
    }

    /**
     * MikroTik API ইনস্ট্যান্স পান
     */
    public function getApi(): MikrotikApi
    {
        return new MikrotikApi(
            $this->ip_address,
            $this->username,
            $this->decrypted_password,
            $this->port
        );
    }

    /**
     * কানেকশন টেস্ট করুন
     */
    public function testConnection(): bool
    {
        $api = $this->getApi();
        $connected = $api->connect();
        
        if ($connected) {
            $this->update(['last_connected_at' => now()]);
            $api->disconnect();
        }
        
        return $connected;
    }
}
