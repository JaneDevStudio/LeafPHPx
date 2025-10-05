<?php

return [
    'default' => getenv('DB_CONNECTION') ?? 'mysql',
    'connections' => [
        'mysql' => [
            'type' => 'mysql',
            'host' => getenv('DB_HOST'),
            'port' => getenv('DB_PORT'),
            'database' => getenv('DB_DATABASE'),
            'username' => getenv('DB_USERNAME'),
            'password' => getenv('DB_PASSWORD'),
        ],
    ],
];