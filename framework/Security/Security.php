<?php

namespace Framework\Security;

use Framework\Security\Auth\AuthManager;
use Framework\Security\Contracts\TokenManagerInterface;
use Framework\Security\Contracts\GuardInterface;
use Framework\Security\Contracts\GateInterface;
use Framework\Security\Contracts\EncrypterInterface;
use Framework\Security\Cookie\CookieManager;
use Framework\Security\Encryption\StringCipher;
use Framework\Security\Session\SessionManager;

class Security
{
    public static function auth(): AuthManager
    {
        return app(AuthManager::class);
    }

    public static function guard(): GuardInterface
    {
        return app(GuardInterface::class);
    }

    public static function gate(): GateInterface
    {
        return app(GateInterface::class);
    }

    public static function session(): SessionManager
    {
        return app(SessionManager::class);
    }

    public static function cookie(): CookieManager
    {
        return app(CookieManager::class);
    }

    public static function csrf(): TokenManagerInterface
    {
        return app(TokenManagerInterface::class);
    }

    public static function cipher(): StringCipher
    {
        return app(StringCipher::class);
    }

    public static function encrypt(): EncrypterInterface
    {
        return app(EncrypterInterface::class);
    }
}
