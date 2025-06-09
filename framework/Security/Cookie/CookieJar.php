<?php

namespace Framework\Security\Cookie;

use Framework\Constants\AuthConstants; 

class CookieJar
{
    protected string $name; 
    protected string $value; 
    protected int $expires; 
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
        string $sameSite = AuthConstants::SAMESITE_LAX
    )
    {
        $this->name = $name; 
        $this->value = $value; 
        $this->expires = $minutes > 0 ? time() + ($minutes * 60) : 0;
        $this->path = $path; 
        $this->domain = $domain; 
        $this->secure = $secure; 
        $this->httpOnly = $httpOnly; 
        $this->sameSite = $sameSite;
    }


    public function send(): void
    {
        if (PHP_VERSION_ID >= 70300) {
            // PHP 7.3 이상: options 배열 사용
            setcookie($this->name, $this->value, [
                'expires'  => $this->expires,
                'path'     => $this->path,
                'domain'   => $this->domain,
                'secure'   => $this->secure,
                'httponly' => $this->httpOnly,
                'samesite' => $this->sameSite, // Lax, Strict, None
            ]);
        } else {
            // PHP 7.2 이하: SameSite는 지원 불가, 경고 로그 출력 등 가능
            setcookie(
                $this->name,
                $this->value,
                $this->expires,
                $this->path,
                $this->domain,
                $this->secure,
                $this->httpOnly
            );
        }
    }
}