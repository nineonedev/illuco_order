<?php

use Framework\Security\Auth\Middleware\AuthMiddleware;
use Framework\Security\Csrf\Middleware\CsrfMiddleware;

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


// function middleware(string|array $names): array
// {
//     $groups = config('middleware.group');

//     return collect((array) $names)
//         ->flatMap(fn($name) => $groups[$name] ?? [$name])
//         ->all();
// }