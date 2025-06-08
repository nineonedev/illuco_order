<?php

namespace Framework\Security\Auth;

use Framework\Security\Contracts\GuardInterface;
use Framework\Support\Facades\Hash;
use RuntimeException;

class AuthManager
{
    /** @var GuardInterface[] */
    protected array $guards = [];

    protected ?string $default = null; 

    public function setGuard(string $name, GuardInterface $guard): void
    {
        $this->guards[$name] = $guard; 
    }

    public function use(string $name): void
    {
        if (!isset($this->guards[$name])) {
            throw new RuntimeException("Guard [$name] is not registered."); 
        }

        $this->default = $name; 
    }

    public function guard(?string $name = null): GuardInterface
    {
        $name = $name ?? $this->default; 

        if (!isset($this->guards[$name])) {
            throw new RuntimeException("Guard [$name] is not registered.");
        }

        return $this->guards[$name]; 
    }

    public function check(): bool
    {
        return $this->guard()->check();
    }

    public function guest(): bool
    {
        return $this->guard()->guest(); 
    }

    public function user()
    {
        return $this->guard()->user(); 
    }

    public function logout(): void
    {
        $this->guard()->logout();
    }
}