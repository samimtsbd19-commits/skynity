<?php

namespace App\Console\Commands;

use App\Services\MikrotikApi;
use App\Models\User;
use App\Models\Voucher;
use App\Models\Package;
use App\Models\StockUser;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class ApplyStockApprovals extends Command
{
    protected $signature = 'stock:apply 
        {--router= : Router ID for filtering} 
        {--url= : API base URL} 
        {--token= : Sanctum token} 
        {--host= : Router IP/Host} 
        {--port=8728 : Router API port} 
        {--user= : Router username} 
        {--pass= : Router password} 
        {--interface=wg0 : WireGuard/Hotspot interface name}';

    protected $description = 'Enable assigned stock users on MikroTik by polling remote API';

    public function handle()
    {
        $url = rtrim($this->option('url') ?? '', '/');
        $token = $this->option('token');
        $routerId = $this->option('router');
        $host = $this->option('host');
        $port = (int) $this->option('port');
        $user = $this->option('user');
        $pass = $this->option('pass');

        if (!$url || !$token || !$host || !$user || !$pass) {
            $this->error('Missing required options: url, token, host, user, pass');
            return 1;
        }

        $resp = Http::withToken($token)->get($url . '/api/stock/pending', [
            'router_id' => $routerId,
        ]);
        if (!$resp->ok()) {
            $this->error('API error: ' . $resp->status());
            return 1;
        }
        $items = $resp->json('data') ?? [];
        if (empty($items)) {
            $this->info('No pending stock approvals');
            return 0;
        }

        $api = new MikrotikApi($host, $user, $pass, $port);
        if (!$api->connect()) {
            $this->error('Router connect failed');
            return 1;
        }

        $count = 0;
        foreach ($items as $item) {
            $ok = $api->enableHotspotUser($item['username']);
            if ($ok && !empty($item['mac'])) {
                $api->updateHotspotUserMac($item['username'], $item['mac']);
            }
            if ($ok) {
                Http::withToken($token)->post($url . '/api/stock/mark-enabled', [
                    'id' => $item['id'],
                ]);
                $count++;
                $this->info('Enabled: ' . $item['username']);
            } else {
                $this->warn('Failed: ' . $item['username']);
            }
        }

        $api->disconnect();
        $this->info('Done, enabled ' . $count);

        // Detect first login and start subscription on first connect
        $api = new MikrotikApi($host, $user, $pass, $port);
        if ($api->connect()) {
            $actives = $api->getActiveUsers();
            $started = 0;
            foreach ($actives as $active) {
                $uname = $active['user'] ?? null;
                if (!$uname) {
                    continue;
                }
                $cust = User::where('hotspot_username', $uname)->first();
                if (!$cust) {
                    continue;
                }
                if (!$cust->subscription_expires_at) {
                    $days = $this->getValidityDays($cust);
                    $expires = now()->addDays($days);
                    $mac = $active['mac-address'] ?? $cust->mac_address;
                    $cust->update([
                        'subscription_expires_at' => $expires,
                        'is_active' => true,
                        'mac_address' => $mac,
                    ]);
                    Voucher::where('username', $uname)->update([
                        'first_login_at' => now(),
                        'expires_at' => $expires,
                        'used_by_mac' => $mac,
                        'status' => 'used',
                    ]);
                    $started++;
                    $this->info('Started: ' . $uname . ' → expires ' . $expires->toDateTimeString());
                }
            }
            $api->disconnect();
            $this->info('First-login started for ' . $started . ' user(s)');
        }
        return 0;
    }

    private function getValidityDays(User $cust): int
    {
        // Prefer stock user validity if exists
        $su = StockUser::where('username', $cust->hotspot_username)->first();
        if ($su && $su->validity_days) {
            return (int) $su->validity_days;
        }
        // Fallback to package validity string
        $pkg = $cust->package_id ? Package::find($cust->package_id) : null;
        $val = $pkg?->validity ?? null;
        if (!$val) {
            return 30;
        }
        if (preg_match('/(\d+)([hdwm])/', $val, $m)) {
            $n = (int) $m[1];
            return match($m[2]) {
                'h' => max(1, (int) ceil($n / 24)),
                'd' => $n,
                'w' => $n * 7,
                'm' => $n * 30,
                default => 30,
            };
        }
        return (int) $val ?: 30;
    }
}
