<?php

namespace Framework\Security\Cookie; 

class CookieJar
{
    protected string $name; 
    protected string $value; 
    protected int $expires; 
    protected string $path; 
    protected string $domain; 
    protected bool $secure; 
    protected bool $httpOnly; 

    public function __construct(
        string $name,
        string $value,
        int $minutes = 0,
        string $path = '/',
        string $domain = '', 
        bool $secure = false,
        bool $httpOnly = true
    )
    {
        $this->name = $name; 
        $this->value = $value; 
        $this->expires = $minutes > 0 ? time() * ($minutes * 60) : 0; 
        $this->path = $path; 
        $this->domain = $domain; 
        $this->secure = $secure; 
        $this->httpOnly = $httpOnly; 
    }

    public function send(): void
    {
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