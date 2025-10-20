<?php

use App\Http\Middlewares\AuthMiddleware;
use App\Http\Middlewares\CsrfMiddleware;
use App\Http\Middlewares\PermissionMiddleware;

return [
    'priority' => [
    ], 
    'global' => [
        
    ],
    'group' => [
        'web' => [
            CsrfMiddleware::class,
        ],
        'auth' => [
            AuthMiddleware::class,
            PermissionMiddleware::class,
        ]
    ],
];