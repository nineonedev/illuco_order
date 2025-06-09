<?php

namespace Framework\Security\Cookie;

use Framework\Constants\AuthConstants; 

class CookieManager
{
    /**
     * @var CookieJar[]
     */
    protected array $queued = [];

    public function get(string $name, $default = null)
    {
        return $_COOKIE[$name] ?? $default;
    }

    public function set(
        string $name,
        string $value,
        int $minutes = 0,
        string $path = '/',
        string $domain = '', 
        ?bool $secure = null,
        bool $httpOnly = true,
        string $samesite = AuthConstants::SAMESITE_LAX
    ): void
    {
        if ($secure === null) {
            $secure = request()->http()->isSecure();
        }

        $cookie = new CookieJar(
            $name, 
            $value, 
            $minutes, 
            $path, 
            $domain, 
            $secure, 
            $httpOnly,
            $samesite
        );
        $this->queued[$name] = $cookie;

        $this->send();
    }

    public function forget(string $name): void
    {
        $this->set($name, '', -60); 
    }

    public function send(): void
    {
        foreach ($this->queued as $cookie) {
            $cookie->send();
        }
    }

    public function has(string $name): bool
    {
        return isset($_COOKIE[$name]); 
    }

    public function queued(): array
    {
        return $this->queued; 
    }
}