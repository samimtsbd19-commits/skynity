<?php

namespace App\Console\Commands;

use App\Models\WireguardPeer;
use Illuminate\Console\Command;

class GenerateWireguardServerConfig extends Command
{
    protected $signature = 'wireguard:server-config 
        {--address= : Server interface address (e.g., 10.7.0.1/24)} 
        {--private= : Server private key (override env)} 
        {--port=51820 : Listen port} 
        {--keepalive= : Persistent keepalive for peers (default from config)}';

    protected $description = 'Generate WireGuard server configuration (wg0.conf) from database peers';

    public function handle(): int
    {
        $serverAddress = $this->option('address') ?: env('WIREGUARD_SERVER_ADDRESS');
        $privateKey = $this->option('private') ?: env('WIREGUARD_SERVER_PRIVATE_KEY');
        $listenPort = (int) ($this->option('port') ?: 51820);
        $keepalive = (int) ($this->option('keepalive') ?: config('wireguard.keepalive'));

        if (!$serverAddress || !$privateKey) {
            $this->error('WIREGUARD_SERVER_ADDRESS and WIREGUARD_SERVER_PRIVATE_KEY must be provided (via options or env).');
            return self::FAILURE;
        }

        $lines = [];
        $lines[] = '[Interface]';
        $lines[] = 'Address = ' . $serverAddress;
        $lines[] = 'PrivateKey = ' . $privateKey;
        $lines[] = 'ListenPort = ' . $listenPort;
        $lines[] = 'SaveConfig = false';
        $lines[] = '';

        $peers = WireguardPeer::query()->orderBy('id')->get();
        foreach ($peers as $peer) {
            $lines[] = '[Peer]';
            $lines[] = 'PublicKey = ' . $peer->public_key;
            $lines[] = 'AllowedIPs = ' . $peer->allowed_address;
            $ka = $peer->persistent_keepalive ?: $keepalive;
            if ($ka) {
                $lines[] = 'PersistentKeepalive = ' . $ka;
            }
            $lines[] = '';
        }

        $config = rtrim(implode("\n", $lines)) . "\n";
        $this->line($config);

        return self::SUCCESS;
    }
}
