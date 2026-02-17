<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SessionLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'router_id',
        'username',
        'mac_address',
        'ip_address',
        'bytes_in',
        'bytes_out',
        'uptime_seconds',
        'started_at',
        'ended_at',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
        'bytes_in' => 'integer',
        'bytes_out' => 'integer',
        'uptime_seconds' => 'integer',
    ];

    public function router()
    {
        return $this->belongsTo(Router::class);
    }

    /**
     * মোট ডাটা পান
     */
    public function getTotalBytesAttribute(): int
    {
        return $this->bytes_in + $this->bytes_out;
    }

    /**
     * ফরম্যাটেড ডাটা পান
     */
    public function getFormattedDataAttribute(): string
    {
        return $this->formatBytes($this->total_bytes);
    }

    /**
     * ফরম্যাটেড আপটাইম পান
     */
    public function getFormattedUptimeAttribute(): string
    {
        $seconds = $this->uptime_seconds;
        
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $secs = $seconds % 60;
        
        if ($hours > 0) {
            return sprintf('%d:%02d:%02d', $hours, $minutes, $secs);
        }
        
        return sprintf('%d:%02d', $minutes, $secs);
    }

    /**
     * বাইট ফরম্যাট করুন
     */
    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes >= 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }
}
