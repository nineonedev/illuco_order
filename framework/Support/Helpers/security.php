<?php

use App\Domains\User\Entities\User;
use Framework\Security\Auth\AuthManager;
use Framework\Security\Auth\Contracts\GuardInterface;
use Framework\Security\Auth\Contracts\GateInterface;
use Framework\Security\Session\SessionManager;
use Framework\Security\Csrf\TokenManagerInterface;
use Framework\Security\Encryption\StringCipher;
use Framework\Security\Encryption\EncrypterInterface;
use Framework\Security\Cookie\CookieManager;
use Framework\Security\Session\Contracts\SessionInterface;

if (!function_exists('auth')) {
    function auth(): AuthManager
    {
        return app(AuthManager::class);
    }
}

if (!function_exists('guard')) {
    function guard(?string $name = null): GuardInterface
    {
        return auth()->guard($name);
    }
}

if (!function_exists('user')) {
    function user(): User
    {
        return guard()->user();
    }
}

if (!function_exists('gate')) {
    function gate(): GateInterface
    {
        return app(GateInterface::class);
    }
}

if (!function_exists('authorize')) {
    function authorize(string $ability, array $arguments = []): bool
    {
        return gate()->authorize($ability, $arguments);
    }
}

if (!function_exists('authorize_or_fail')) {
    function authorize_or_fail(string $ability, array $arguments = []): void
    {
        gate()->authorizeOrFail($ability, $arguments);
    }
}

if (!function_exists('can')) {
    function can(string $ability, array $arguments = []): bool
    {
        $user = user();

        if (!$user) return false;

        return gate()->allows($ability, array_merge([$user], $arguments));
    }
}

if (!function_exists('cannot')) {
    function cannot(string $ability, array $arguments = []): bool
    {
        return !can($ability, $arguments);
    }
}


if (!function_exists('cipher')) {
    function cipher(): StringCipher
    {
        return app(StringCipher::class);
    }
}

if (!function_exists('encryptor')) {
    function encryptor(): EncrypterInterface
    {
        return app(EncrypterInterface::class);
    }
}

if (!function_exists('cookie')) {
    function cookie(): CookieManager
    {
        return app(CookieManager::class);
    }
}


if (!function_exists('csrf')) {
    function csrf(): TokenManagerInterface
    {
        return app(TokenManagerInterface::class);
    }
}

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

if (!function_exists('csrf_meta')) {
    function csrf_meta(): string
    {
        return app(TokenManagerInterface::class)->meta();
    }
}


if (!function_exists('session')) {
    function session(): SessionManager
    {
        return app(SessionManager::class);
    }
}


if (!function_exists('session_driver')) {
    function session_driver($name = null): SessionInterface
    {
        return session()->driver($name);
    }
}

if (!function_exists('flash')) {
    function flash($key = null, $value = null)
    {
        $flashBag = session()->flashBag();

        if ($key === null) {
            return $flashBag;
        }

        if (is_array($key)) {
            $flashBag->setMany($key);
            return;
        }

        $flashBag->set($key, $value);
    }
}


if (!function_exists('errors')) {
    function errors(): array
    {
        return flash()->getErrors();
    }
}

if (!function_exists('has_errors')) {
    function has_errors(string $key): bool
    {
        return isset(errors()[$key]);
    }
}

if (!function_exists('old')) {
    function old(string $key, $default = null)
    {
        return flash()->getInput($key, $default);
    }
}
