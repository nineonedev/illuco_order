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


// 'web' => [
//     \App\Http\Middleware\EncryptCookies::class,
//     \Illuminate\Session\Middleware\StartSession::class,
//     \Illuminate\View\Middleware\ShareErrorsFromSession::class,
//     \App\Http\Middleware\VerifyCsrfToken::class,
// ]
