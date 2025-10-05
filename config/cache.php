<?php

return [
    'driver' => getenv('CACHE_DRIVER') ?? 'file',
    'file' => [
        'path' => __DIR__ . '/../storage/cache/',
    ],
    'redis' => [
        'host' => '127.0.0.1',
        'port' => 6379,
    ],
];