<?php

namespace Framework\Security\Session;

use Framework\Security\Contracts\SessionInterface;

class InMemoryStore implements SessionInterface
{
    protected array $storage = [];
    protected string $id; 

    public function __construct()
    {
        $this->id = bin2hex(random_bytes(16));
    }

    public function start(): void
    {
        // 메모리 기반이므로 아무 동작 없음
    }

    public function has(string $key): bool
    {
        return isset($this->storage[$key]); 
    }

    public function get(string $key, $default = null)
    {
        return $this->storage[$key] ?? $default; 
    }

    public function set(string $key, $value): void
    {
        $this->storage[$key] = $value; 
    }

    public function forget(string $key): void
    {
        unset($this->storage[$key]); 
    }

    public function flush(): void
    {
        $this->storage = []; 
    }

    public function all(): array
    {
        return $this->storage;
    }

    public function id(): string
    {
        return $this->id; 
    }

    public function regenerate(): void
    {
        $this->id = bin2hex(random_bytes(16)); 
    }

    public function invalidate(): void
    {
        $this->flush();
        $this->regenerate();
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