<?php 

use Framework\Security\Csrf\TokenManagerInterface;

if (!function_exists('csrf_token')) {
    function csrf_token(): ?string
    {
        return app(TokenManagerInterface::class)->token();
    }
}

if (!function_exists('verify_csrf')) {
    function verify_csrf(string $token): bool
    {
        return app(TokenManagerInterface::class)->verify($token); 
    }
}

if (!function_exists('csrf_field')) {
    function csrf_field(): string
    {
        return app(TokenManagerInterface::class)->field();
    }
}
