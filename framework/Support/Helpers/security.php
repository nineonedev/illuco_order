<?php

use Framework\Core\Application;
use Framework\Security\Auth\AuthManager;
use Framework\Security\Contracts\SessionInterface;
use Framework\Security\Contracts\TokenManagerInterface;
use Framework\Security\Cookie\CookieManager;
use Framework\Security\Session\DatabaseStore;
use Framework\Security\Session\SessionManager;
use Framework\Support\Facades\Gate;

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

if (!function_exists('session')) {
    function session(): SessionManager
    {
        return app(SessionManager::class);
    }
}

if (!function_exists('session_store')) {
    function session_store(): DatabaseStore 
    {
        return session()->driver('db');
    }
}

if (!function_exists('session_driver')) {
    function session_driver($name = null): SessionInterface
    {
        return session()->driver($name);
    }
}

if (!function_exists('cookie')) {
    function cookie(): CookieManager
    {
        return app(CookieManager::class);
    }
}

if (!function_exists('auth')) {
    function auth(): AuthManager
    {
        return app(AuthManager::class);
    }
}

if (!function_exists('authorize')) {
    function authorize(string $ability, array $arguments = []): void
    {
        $user = auth()->user();

        if (!$user) {
            throw new \RuntimeException("Unauthorized: No authenticated user.");
        }

        $args = array_merge([$user], $arguments);

        if (!Gate::allows($ability, $args)) {
            throw new \RuntimeException("403 Forbidden: You are not authorized to perform [$ability].");
        }
    }
}

if (!function_exists('can')) {
    function can(string $ability, array $arguments = []): bool
    {
        $user = auth()->user();

        if (!$user) {
            return false;
        }

        $args = array_merge([$user], $arguments);

        return Gate::allows($ability, $args);
    }
}

if (!function_exists('cannot')) {
    function cannot(string $ability, array $arguments = []): bool
    {
        return !can($ability, $arguments);
    }
}