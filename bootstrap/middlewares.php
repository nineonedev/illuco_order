<?php

use Framework\Security\Csrf\Middleware\CsrfMiddleware;

return [
    'priority' => [
    ], 
    'global' => [
        CsrfMiddleware::class
    ],
    'group' => [

    ],
];