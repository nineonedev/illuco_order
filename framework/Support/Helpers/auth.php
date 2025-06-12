<?php

use Framework\Security\Auth\AuthManager;
use Framework\Support\Facades\Gate;


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