<?php

use App\Http\Middlewares\AuthMiddleware;
use App\Http\Middlewares\CsrfMiddleware;

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
        ]
    ],
];