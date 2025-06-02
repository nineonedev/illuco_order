<?php

namespace Framework\Security\Session;

use Framework\Security\Contracts\SessionInterface;
use RuntimeException;

class SessionManager
{
    /**
     * @var SessionInterface[]
     */
    protected array $drivers = [];

    protected ?string $current = null;

    public function setDriver(string $name, SessionInterface $driver): void
    {
        $this->drivers[$name] = $driver;
    }

    public function use(string $name): void
    {
        if (!isset($this->drivers[$name])) {
            throw new RuntimeException("Session driver [$name] not registered."); 
        }

        $this->current = $name; 
        $this->drivers[$name]->start(); 
    }
    
    public function driver(?string $name = null): SessionInterface
    {
        $name = $name ?? $this->current; 

        if (!isset($this->drivers[$name])) {
            throw new RuntimeException("Session driver [$name] not registered.");
        }

        return $this->drivers[$name]; 
    }

    public function get(string $key, $default = null)
    {
        return $this->driver()->get($key, $default);
    }

    public function set(string $key, $value): void
    {
        $this->driver()->set($key, $value);
    }

    public function has(string $key): bool
    {
        return $this->driver()->has($key);
    }

    public function forget(string $key): void
    {
        $this->driver()->forget($key);
    }

    public function flush(): void
    {
        $this->driver()->flush();
    }

    public function all(): array
    {
        return $this->driver()->all();
    }

    public function regenerate(): void
    {
        $this->driver()->regenerate();
    }

    public function invalidate(): void
    {
        $this->driver()->invalidate();
    }

    public function id(): string
    {
        return $this->driver()->id();
    }
}