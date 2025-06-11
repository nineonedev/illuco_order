<?php

namespace Framework\Security\Cookie;

class CookieJar
{
    const SAME_SITE_LAX = 'Lax';
    const SAME_SITE_STRICT = 'Strict';
    const SAME_SITE_NONE = 'None';

    protected string $name;
    protected string $value;
    protected int $minutes;
    protected string $path;
    protected string $domain;
    protected bool $secure;
    protected bool $httpOnly;
    protected string $sameSite;

    public function __construct(
        string $name,
        string $value,
        int $minutes = 0,
        string $path = '/',
        string $domain = '',
        bool $secure = false,
        bool $httpOnly = true,
        string $sameSite = self::SAME_SITE_LAX
    ) {
        $this->name = $name;
        $this->value = $value;
        $this->minutes = $minutes;
        $this->path = $path;
        $this->domain = $domain;
        $this->secure = $secure;
        $this->httpOnly = $httpOnly;
        $this->sameSite = $sameSite;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getDomain(): string
    {
        return $this->domain;
    }

    public function isSecure(): bool
    {
        return $this->secure;
    }

    public function isHttpOnly(): bool
    {
        return $this->httpOnly;
    }

    public function getSameSite(): string
    {
        return $this->sameSite;
    }

    public function send(): void
    {
        $expire = $this->minutes > 0 ? time() + ($this->minutes * 60) : 0;

        // PHP 7.3+ 지원을 위해 배열 옵션 사용
        setcookie($this->name, $this->value, [
            'expires'  => $expire,
            'path'     => $this->path,
            'domain'   => $this->domain,
            'secure'   => $this->secure,
            'httponly' => $this->httpOnly,
            'samesite' => $this->sameSite,
        ]);
    }

    public function isExpired(): bool
    {
        return $this->minutes < 0;
    }
}
