<?php

use Framework\Security\Csrf\Middleware\CsrfMiddleware;

return [
    'priority' => [
        CsrfMiddleware::class
    ], 
    'global' => [

    ],
    'group' => [

    ],
];