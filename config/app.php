<?php

return [
    'env' => getenv('APP_ENV') ?? 'production',
    'debug' => (bool) (getenv('APP_DEBUG') ?? false),
    'modules' => [
        'database' => true,  // Load optional modules
        'cache' => true,
        'validation' => true,
        'view' => 'php',  // 'php' or 'twig'
    ],
];