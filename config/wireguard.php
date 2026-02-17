<?php

return [
    'enabled' => env('WIREGUARD_ENABLED', false),
    'endpoint' => env('WIREGUARD_ENDPOINT'),
    'server_public_key' => env('WIREGUARD_SERVER_PUBLIC_KEY'),
    'subnet' => env('WIREGUARD_SUBNET', '10.7.0.0/24'),
    'dns' => env('WIREGUARD_DNS', '1.1.1.1'),
    'keepalive' => (int) env('WIREGUARD_PERSISTENT_KEEPALIVE', 25),
];
