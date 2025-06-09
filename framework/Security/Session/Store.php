<?php

namespace Framework\Security\Session;

use Framework\Security\Contracts\SessionInterface;

class Store implements SessionInterface
{
    protected bool $started = false; 

    public function start(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start(); 
        }

        $this->started = true; 
    }

    public function has(string $key): bool
    {
        return isset($_SESSION[$key]); 
    }

    public function get(string $key, $default = null)
    {
        return $_SESSION[$key] ?? $default; 
    }

    public function set(string $key, $value): void
    {
        $_SESSION[$key] = $value; 
    }

    public function forget(string $key): void
    {
        unset($_SESSION[$key]); 
    }

    public function flush(): void
    {
        $_SESSION = [];
    }

    public function all(): array
    {
        return $_SESSION; 
    }

    public function id(): string
    {
        return session_id();
    }

    public function regenerate(): void
    {
        session_regenerate_id(true);
    }

    public function invalidate(): void
    {
        $_SESSION = []; 
        session_destroy();
    }

    public function userId(): ?int
    {
        $userId = $this->get('user_id');
        return $userId ? (int) $userId : null;
    }

    public function setUserId(?int $userId = null): void
    {
        $this->set('user_id', $userId);
    }
}