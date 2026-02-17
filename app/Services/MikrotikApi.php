<?php

namespace App\Services;

/**
 * SKYNITY MikroTik RouterOS API
 * MikroTik রাউটারের সাথে যোগাযোগের জন্য
 */
class MikrotikApi
{
    private $socket;
    private $debug = false;
    private $connected = false;
    private $timeout = 3;
    private $attempts = 5;
    private $delay = 3;

    private $host;
    private $port;
    private $username;
    private $password;

    public function __construct(string $host, string $username, string $password, int $port = 8728)
    {
        $this->host = $host;
        $this->port = $port;
        $this->username = $username;
        $this->password = $password;
    }

    /**
     * রাউটারের সাথে কানেক্ট করুন
     */
    public function connect(): bool
    {
        for ($attempt = 1; $attempt <= $this->attempts; $attempt++) {
            $this->socket = @fsockopen($this->host, $this->port, $errno, $errstr, $this->timeout);
            
            if ($this->socket) {
                socket_set_timeout($this->socket, $this->timeout);
                
                if ($this->login()) {
                    $this->connected = true;
                    return true;
                }
            }
            
            sleep($this->delay);
        }
        
        return false;
    }

    /**
     * লগইন করুন
     */
    private function login(): bool
    {
        // New auth method (RouterOS 6.43+)
        $response = $this->command('/login', [
            '=name=' . $this->username,
            '=password=' . $this->password,
        ]);

        if (isset($response[0]) && $response[0] === '!done') {
            return true;
        }

        // Old auth method
        if (isset($response[1]['=ret'])) {
            $challenge = $response[1]['=ret'];
            $response = $this->command('/login', [
                '=name=' . $this->username,
                '=response=00' . md5(chr(0) . $this->password . pack('H*', $challenge)),
            ]);
            
            return isset($response[0]) && $response[0] === '!done';
        }

        return false;
    }

    /**
     * কানেকশন বন্ধ করুন
     */
    public function disconnect(): void
    {
        if ($this->socket) {
            fclose($this->socket);
        }
        $this->connected = false;
    }

    /**
     * কমান্ড পাঠান
     */
    public function command(string $command, array $attributes = []): array
    {
        $this->write($command);
        
        foreach ($attributes as $attr) {
            $this->write($attr);
        }
        
        $this->write('');
        
        return $this->read();
    }

    /**
     * ডাটা লিখুন
     */
    private function write(string $data): void
    {
        $length = strlen($data);
        
        if ($length < 0x80) {
            fwrite($this->socket, chr($length));
        } elseif ($length < 0x4000) {
            fwrite($this->socket, chr(($length >> 8) | 0x80) . chr($length & 0xFF));
        } elseif ($length < 0x200000) {
            fwrite($this->socket, chr(($length >> 16) | 0xC0) . chr(($length >> 8) & 0xFF) . chr($length & 0xFF));
        } elseif ($length < 0x10000000) {
            fwrite($this->socket, chr(($length >> 24) | 0xE0) . chr(($length >> 16) & 0xFF) . chr(($length >> 8) & 0xFF) . chr($length & 0xFF));
        } else {
            fwrite($this->socket, chr(0xF0) . chr(($length >> 24) & 0xFF) . chr(($length >> 16) & 0xFF) . chr(($length >> 8) & 0xFF) . chr($length & 0xFF));
        }
        
        fwrite($this->socket, $data);
    }

    /**
     * ডাটা পড়ুন
     */
    private function read(): array
    {
        $response = [];

        while (true) {
            $byte = @fread($this->socket, 1);
            
            // If no data, wait a bit and try again
            if ($byte === '' || $byte === false) {
                $info = stream_get_meta_data($this->socket);
                if ($info['timed_out'] || $info['eof']) {
                    break;
                }
                usleep(100000); // 100ms delay
                $byte = @fread($this->socket, 1);
                if ($byte === '' || $byte === false) {
                    break;
                }
            }
            
            $byte = ord($byte);
            
            if ($byte < 0x80) {
                $length = $byte;
            } elseif ($byte < 0xC0) {
                $length = (($byte & 0x3F) << 8) + ord(fread($this->socket, 1));
            } elseif ($byte < 0xE0) {
                $length = (($byte & 0x1F) << 16) + (ord(fread($this->socket, 1)) << 8) + ord(fread($this->socket, 1));
            } elseif ($byte < 0xF0) {
                $length = (($byte & 0x0F) << 24) + (ord(fread($this->socket, 1)) << 16) + (ord(fread($this->socket, 1)) << 8) + ord(fread($this->socket, 1));
            } else {
                $length = (ord(fread($this->socket, 1)) << 24) + (ord(fread($this->socket, 1)) << 16) + (ord(fread($this->socket, 1)) << 8) + ord(fread($this->socket, 1));
            }

            if ($length == 0) {
                continue;
            }
            
            $receivedData = '';
            while ($length > 0) {
                $toRead = min($length, 8192);
                $chunk = fread($this->socket, $toRead);
                if ($chunk === false) break;
                $receivedData .= $chunk;
                $length -= strlen($chunk);
            }
            
            $response[] = $receivedData;

            if ($receivedData === '!done' || strpos($receivedData, '!trap') === 0) {
                break;
            }
        }

        return $this->parseResponse($response);
    }

    /**
     * রেসপন্স পার্স করুন
     */
    private function parseResponse(array $response): array
    {
        $parsed = [];
        $current = [];

        foreach ($response as $line) {
            if (strpos($line, '!') === 0) {
                if (!empty($current)) {
                    $parsed[] = $current;
                    $current = [];
                }
                $parsed[] = $line;
            } elseif (strpos($line, '=') === 0) {
                $parts = explode('=', substr($line, 1), 2);
                if (count($parts) === 2) {
                    $current[$parts[0]] = $parts[1];
                }
            }
        }

        if (!empty($current)) {
            $parsed[] = $current;
        }

        return $parsed;
    }

    // ========================================
    // হটস্পট ইউজার ম্যানেজমেন্ট
    // ========================================

    /**
     * সব হটস্পট ইউজার পান
     */
    public function getHotspotUsers(): array
    {
        $response = $this->command('/ip/hotspot/user/print');
        return $this->filterData($response);
    }

    /**
     * নতুন হটস্পট ইউজার তৈরি করুন
     */
    public function addHotspotUser(string $name, string $password, string $profile, string $comment = '', string $limitUptime = '', string $limitBytes = ''): bool
    {
        $attributes = [
            '=name=' . $name,
            '=password=' . $password,
            '=profile=' . $profile,
        ];

        if ($comment) {
            $attributes[] = '=comment=' . $comment;
        }
        if ($limitUptime) {
            $attributes[] = '=limit-uptime=' . $limitUptime;
        }
        if ($limitBytes) {
            $attributes[] = '=limit-bytes-total=' . $limitBytes;
        }

        $response = $this->command('/ip/hotspot/user/add', $attributes);
        // Check if response contains !done anywhere
        foreach ($response as $item) {
            if ($item === '!done' || (is_array($item) && isset($item['ret']))) {
                return true;
            }
        }
        return false;
    }

    /**
     * হটস্পট ইউজার ডিলিট করুন
     */
    public function removeHotspotUser(string $id): bool
    {
        $response = $this->command('/ip/hotspot/user/remove', ['=.id=' . $id]);
        return $this->isSuccess($response);
    }

    /**
     * হটস্পট ইউজার এডিট করুন
     */
    public function editHotspotUser(string $id, array $data): bool
    {
        $attributes = ['=.id=' . $id];
        
        foreach ($data as $key => $value) {
            $attributes[] = '=' . $key . '=' . $value;
        }

        $response = $this->command('/ip/hotspot/user/set', $attributes);
        return $this->isSuccess($response);
    }

    /**
     * হটস্পট ইউজার সক্রিয়/নিষ্ক্রিয় করুন
     */
    public function toggleHotspotUser(string $id, bool $disabled): bool
    {
        $response = $this->command('/ip/hotspot/user/set', [
            '=.id=' . $id,
            '=disabled=' . ($disabled ? 'yes' : 'no'),
        ]);
        return $this->isSuccess($response);
    }

    /**
     * হটস্পট ইউজার Enable করুন (নাম দিয়ে)
     */
    public function enableHotspotUser(string $name): bool
    {
        // প্রথমে ইউজার খুঁজি
        $response = $this->command('/ip/hotspot/user/print', [
            '?name=' . $name
        ]);
        
        $users = $this->filterData($response);
        if (empty($users)) {
            return false;
        }

        $userId = $users[0]['.id'] ?? null;
        if (!$userId) {
            return false;
        }

        // Enable করি
        $response = $this->command('/ip/hotspot/user/set', [
            '=.id=' . $userId,
            '=disabled=no',
        ]);
        return $this->isSuccess($response);
    }

    /**
     * হটস্পট ইউজার Disable করুন (নাম দিয়ে)
     */
    public function disableHotspotUser(string $name): bool
    {
        $response = $this->command('/ip/hotspot/user/print', [
            '?name=' . $name
        ]);
        
        $users = $this->filterData($response);
        if (empty($users)) {
            return false;
        }

        $userId = $users[0]['.id'] ?? null;
        if (!$userId) {
            return false;
        }

        $response = $this->command('/ip/hotspot/user/set', [
            '=.id=' . $userId,
            '=disabled=yes',
        ]);
        return $this->isSuccess($response);
    }

    /**
     * হটস্পট ইউজারের MAC আপডেট করুন
     */
    public function updateHotspotUserMac(string $name, string $mac): bool
    {
        $response = $this->command('/ip/hotspot/user/print', [
            '?name=' . $name
        ]);
        
        $users = $this->filterData($response);
        if (empty($users)) {
            return false;
        }

        $userId = $users[0]['.id'] ?? null;
        if (!$userId) {
            return false;
        }

        $response = $this->command('/ip/hotspot/user/set', [
            '=.id=' . $userId,
            '=mac-address=' . $mac,
        ]);
        return $this->isSuccess($response);
    }

    /**
     * হটস্পট ইউজার Disabled হিসেবে তৈরি করুন (স্টক ইউজার)
     */
    public function addDisabledHotspotUser(string $name, string $password, string $profile, string $comment = ''): bool
    {
        $attributes = [
            '=name=' . $name,
            '=password=' . $password,
            '=profile=' . $profile,
            '=disabled=yes',
        ];

        if ($comment) {
            $attributes[] = '=comment=' . $comment;
        }

        $response = $this->command('/ip/hotspot/user/add', $attributes);
        foreach ($response as $item) {
            if ($item === '!done' || (is_array($item) && isset($item['ret']))) {
                return true;
            }
        }
        return false;
    }

    // ========================================
    // হটস্পট প্রোফাইল ম্যানেজমেন্ট
    // ========================================

    /**
     * সব প্রোফাইল পান
     */
    public function getHotspotProfiles(): array
    {
        $response = $this->command('/ip/hotspot/user/profile/print');
        return $this->filterData($response);
    }

    /**
     * নতুন প্রোফাইল তৈরি করুন
     */
    public function addHotspotProfile(string $name, string $rateLimit = '', string $sharedUsers = '1'): bool
    {
        $attributes = [
            '=name=' . $name,
            '=shared-users=' . $sharedUsers,
        ];

        if ($rateLimit) {
            $attributes[] = '=rate-limit=' . $rateLimit;
        }

        $response = $this->command('/ip/hotspot/user/profile/add', $attributes);
        return isset($response[0]) && $response[0] === '!done';
    }

    // ========================================
    // হটস্পট অ্যাক্টিভ ইউজার
    // ========================================

    /**
     * অ্যাক্টিভ ইউজার পান
     */
    public function getActiveUsers(): array
    {
        $response = $this->command('/ip/hotspot/active/print');
        return $this->filterData($response);
    }

    /**
     * অ্যাক্টিভ ইউজার কিক করুন
     */
    public function kickActiveUser(string $id): bool
    {
        $response = $this->command('/ip/hotspot/active/remove', ['=.id=' . $id]);
        return isset($response[0]) && $response[0] === '!done';
    }

    // ========================================
    // সিস্টেম ইনফরমেশন
    // ========================================

    /**
     * রাউটার রিসোর্স পান
     */
    public function getSystemResource(): array
    {
        $response = $this->command('/system/resource/print');
        $filtered = $this->filterData($response);
        // Get first array element from filtered results
        foreach ($filtered as $item) {
            if (is_array($item) && isset($item['uptime'])) {
                return $item;
            }
        }
        return $filtered[0] ?? [];
    }

    /**
     * রাউটার আইডেন্টিটি পান
     */
    public function getIdentity(): string
    {
        $response = $this->command('/system/identity/print');
        $data = $this->filterData($response);
        return $data[0]['name'] ?? 'MikroTik';
    }

    /**
     * রাউটার রিবুট করুন
     */
    public function reboot(): bool
    {
        $response = $this->command('/system/reboot');
        return true;
    }

    // ========================================
    // IP Binding
    // ========================================

    /**
     * IP Binding পান
     */
    public function getIpBindings(): array
    {
        $response = $this->command('/ip/hotspot/ip-binding/print');
        return $this->filterData($response);
    }

    /**
     * IP Binding যোগ করুন
     */
    public function addIpBinding(string $mac, string $type = 'bypassed', string $comment = ''): bool
    {
        $attributes = [
            '=mac-address=' . $mac,
            '=type=' . $type,
        ];

        if ($comment) {
            $attributes[] = '=comment=' . $comment;
        }

        $response = $this->command('/ip/hotspot/ip-binding/add', $attributes);
        return isset($response[0]) && $response[0] === '!done';
    }

    // ========================================
    // DHCP Leases
    // ========================================

    /**
     * DHCP Leases পান
     */
    public function getDhcpLeases(): array
    {
        $response = $this->command('/ip/dhcp-server/lease/print');
        return $this->filterData($response);
    }

    // ========================================
    // হেল্পার ফাংশন
    // ========================================

    /**
     * রেসপন্স সফল কিনা চেক করুন
     */
    private function isSuccess(array $response): bool
    {
        foreach ($response as $item) {
            if ($item === '!done') {
                return true;
            }
            if (is_array($item) && isset($item['ret'])) {
                return true;
            }
        }
        // Check for trap (error)
        foreach ($response as $item) {
            if ($item === '!trap' || (is_string($item) && strpos($item, '!trap') === 0)) {
                return false;
            }
        }
        return !empty($response);
    }

    /**
     * ডাটা ফিল্টার করুন
     */
    private function filterData(array $response): array
    {
        $result = [];
        foreach ($response as $item) {
            if (is_array($item)) {
                $result[] = $item;
            }
        }
        return $result;
    }

    /**
     * কানেকশন চেক করুন
     */
    public function isConnected(): bool
    {
        return $this->connected;
    }

    /**
     * পিং টেস্ট
     */
    public function ping(): bool
    {
        if (!$this->connected) {
            return $this->connect();
        }
        
        $response = $this->command('/system/identity/print');
        return !empty($response);
    }
    
    public function getWireguardInterfaces(): array
    {
        $response = $this->command('/interface/wireguard/print');
        return $this->filterData($response);
    }

    public function ensureWireguardInterface(string $name, int $listenPort = 51820): bool
    {
        $existing = $this->command('/interface/wireguard/print', ['?name=' . $name]);
        $data = $this->filterData($existing);
        if (!empty($data)) {
            return true;
        }
        $resp = $this->command('/interface/wireguard/add', [
            '=name=' . $name,
            '=listen-port=' . $listenPort,
        ]);
        return $this->isSuccess($resp);
    }

    public function getWireguardPeers(string $interface): array
    {
        $response = $this->command('/interface/wireguard/peers/print', [
            '?interface=' . $interface,
        ]);
        return $this->filterData($response);
    }

    public function addWireguardPeer(string $interface, string $publicKey, string $allowedAddress, string $comment = '', ?string $presharedKey = null, ?int $persistentKeepalive = null): bool
    {
        $attrs = [
            '=interface=' . $interface,
            '=public-key=' . $publicKey,
            '=allowed-address=' . $allowedAddress,
        ];
        if ($comment !== '') {
            $attrs[] = '=comment=' . $comment;
        }
        if ($presharedKey) {
            $attrs[] = '=preshared-key=' . $presharedKey;
        }
        if ($persistentKeepalive) {
            $attrs[] = '=persistent-keepalive=' . $persistentKeepalive;
        }
        $resp = $this->command('/interface/wireguard/peers/add', $attrs);
        return $this->isSuccess($resp);
    }

    public function removeWireguardPeerById(string $id): bool
    {
        $resp = $this->command('/interface/wireguard/peers/remove', ['=.id=' . $id]);
        return $this->isSuccess($resp);
    }

    public function removeWireguardPeerByPublicKey(string $publicKey): bool
    {
        $peers = $this->command('/interface/wireguard/peers/print', ['?public-key=' . $publicKey]);
        $data = $this->filterData($peers);
        if (empty($data)) {
            return false;
        }
        $id = $data[0]['.id'] ?? null;
        if (!$id) {
            return false;
        }
        return $this->removeWireguardPeerById($id);
    }
}
