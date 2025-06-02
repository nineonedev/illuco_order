<?php 

return [
    'default' => 'mysql',
    'migrations' => [
        'path' => base_path('database/migrations'),
    ],
    'connections' => [
        'mysql' => [
            'driver' => 'mysql',
            'host'       => env('DB_HOST', 'db'),
            'database'   => env('DB_DATABASE', 'nineonelabs'),
            'username'   => env('DB_USERNAME', 'user'),
            'password'   => env('DB_PASSWORD', 'password'),
            'port'       => env('DB_PORT', 3306),
            'charset'    => env('DB_CHARSET', 'utf8mb4'),
        ]
    ]
];