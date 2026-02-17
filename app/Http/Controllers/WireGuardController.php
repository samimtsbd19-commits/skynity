<?php

namespace App\Http\Controllers;

use App\Models\Router;
use App\Models\WireguardPeer;
use Illuminate\Http\Request;

class WireGuardController extends Controller
{
    public function addPeer(Request $request)
    {
        $data = $request->validate([
            'router_id' => 'required|exists:routers,id',
            'public_key' => 'required|string',
            'comment' => 'nullable|string',
        ]);

        $router = Router::findOrFail($data['router_id']);
        $interface = $router->wireguard_interface ?: 'wg0';
        $subnet = config('wireguard.subnet');
        $keepalive = config('wireguard.keepalive');

        $allocated = WireguardPeer::where('router_id', $router->id)->pluck('allowed_address')->toArray();
        $allowedAddress = $this->allocateAddress($subnet, $allocated);
        if (!$allowedAddress) {
            return response()->json(['error' => 'No available address'], 422);
        }

        $api = $router->getApi();
        if (!$api->connect()) {
            return response()->json(['error' => 'Router unreachable'], 502);
        }
        $api->ensureWireguardInterface($interface);
        $ok = $api->addWireguardPeer($interface, $data['public_key'], $allowedAddress, $data['comment'] ?? '', null, $keepalive);
        $api->disconnect();
        if (!$ok) {
            return response()->json(['error' => 'Failed to add peer'], 500);
        }

        $peer = WireguardPeer::create([
            'router_id' => $router->id,
            'user_id' => auth()->id(),
            'interface' => $interface,
            'public_key' => $data['public_key'],
            'allowed_address' => $allowedAddress,
            'comment' => $data['comment'] ?? null,
            'persistent_keepalive' => $keepalive,
            'status' => 'active',
        ]);

        return response()->json([
            'id' => $peer->id,
            'allowed_address' => $allowedAddress,
            'endpoint' => config('wireguard.endpoint'),
            'server_public_key' => config('wireguard.server_public_key'),
            'dns' => config('wireguard.dns'),
        ]);
    }

    public function removePeer(Request $request, int $id)
    {
        $peer = WireguardPeer::findOrFail($id);
        $router = $peer->router;
        $api = $router->getApi();
        if ($api->connect()) {
            $peers = $api->getWireguardPeers($peer->interface);
            $targetId = null;
            foreach ($peers as $p) {
                if (($p['public-key'] ?? '') === $peer->public_key) {
                    $targetId = $p['.id'] ?? null;
                    break;
                }
            }
            if ($targetId) {
                $api->removeWireguardPeerById($targetId);
            }
            $api->disconnect();
        }
        $peer->delete();
        return response()->json(['ok' => true]);
    }

    public function config(Request $request, int $id)
    {
        $data = $request->validate([
            'private_key' => 'required|string',
        ]);
        $peer = WireguardPeer::findOrFail($id);
        $router = $peer->router;
        $endpoint = config('wireguard.endpoint');
        $serverPublicKey = config('wireguard.server_public_key');
        $dns = config('wireguard.dns');
        $keepalive = (int) config('wireguard.keepalive');
        $content = $this->buildClientConfig(
            $data['private_key'],
            $peer->allowed_address,
            $dns,
            $endpoint,
            $serverPublicKey,
            $keepalive
        );
        return response($content, 200, [
            'Content-Type' => 'text/plain',
        ]);
    }

    private function allocateAddress(string $cidr, array $used): ?string
    {
        [$network, $mask] = explode('/', $cidr);
        $start = ip2long($network);
        $size = 2 ** (32 - (int) $mask);
        for ($i = 2; $i < $size - 1; $i++) {
            $ip = long2ip($start + $i);
            $addr = $ip . '/32';
            if (!in_array($addr, $used, true)) {
                return $addr;
            }
        }
        return null;
    }

    private function buildClientConfig(string $privateKey, string $address, string $dns, string $endpoint, string $serverPublicKey, int $keepalive): string
    {
        $lines = [
            '[Interface]',
            'PrivateKey = ' . $privateKey,
            'Address = ' . $address,
            'DNS = ' . $dns,
            '',
            '[Peer]',
            'PublicKey = ' . $serverPublicKey,
            'Endpoint = ' . $endpoint,
            'AllowedIPs = 0.0.0.0/0, ::/0',
            'PersistentKeepalive = ' . $keepalive,
        ];
        return implode("\n", $lines) . "\n";
    }
}
