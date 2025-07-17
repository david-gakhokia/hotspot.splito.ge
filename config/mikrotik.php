<?php

return [
    'host' => env('MIKROTIK_HOST', '192.168.88.1'),
    'port' => env('MIKROTIK_PORT', 8728),
    'user' => env('MIKROTIK_USER'),
    'pass' => env('MIKROTIK_PASS'),
    'timeout' => env('MIKROTIK_TIMEOUT', 3),
];
