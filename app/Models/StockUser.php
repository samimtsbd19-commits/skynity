<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockUser extends Model
{
    use HasFactory;

    protected $fillable = [
        'router_id',
        'package_id',
        'username',
        'password',
        'profile',
        'speed_limit',
        'validity_days',
        'status',
        'assigned_to_request_id',
        'assigned_at',
        'created_in_mikrotik_at',
        'enabled_on_router_at',
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
        'created_in_mikrotik_at' => 'datetime',
        'enabled_on_router_at' => 'datetime',
    ];

    public function router()
    {
        return $this->belongsTo(Router::class);
    }

    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    public function hotspotRequest()
    {
        return $this->belongsTo(HotspotRequest::class, 'assigned_to_request_id');
    }

    public function scopeAvailable($query)
    {
        return $query->where('status', 'available');
    }

    public function scopeForRouter($query, $routerId)
    {
        return $query->where('router_id', $routerId);
    }

    public static function getAvailableCount($routerId = null)
    {
        $query = self::where('status', 'available');
        if ($routerId) {
            $query->where('router_id', $routerId);
        }
        return $query->count();
    }

    public static function getAssignedCount($routerId = null)
    {
        $query = self::where('status', 'assigned');
        if ($routerId) {
            $query->where('router_id', $routerId);
        }
        return $query->count();
    }

    public static function assignToRequest($routerId, $requestId, $packageId = null)
    {
        $query = self::where('router_id', $routerId)->where('status', 'available');
        
        if ($packageId) {
            $query->where('package_id', $packageId);
        }
        
        $user = $query->first();
        
        if ($user) {
            $user->update([
                'status' => 'assigned',
                'assigned_to_request_id' => $requestId,
                'assigned_at' => now(),
            ]);
            return $user;
        }
        
        return null;
    }
}
