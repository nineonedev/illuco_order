<?php

namespace Framework\Security\Session;

use Framework\Security\Session\Contracts\SessionInterface;
use Framework\Security\Session\Bags\FlashBag;
use Framework\Security\Session\Bags\SessionBag;

abstract class SessionStore implements SessionInterface
{
    const SESSION_ID = 'SESSION_ID'; 

    protected ?string $id = null;
    protected array $data = [];
    protected bool $started = false;

    protected ?FlashBag $flashBag = null;

    public function isStarted(): bool
    {
        return $this->started;
    }

    public function start(): void
    {
        $this->started = true;

        if (!$this->flashBag) {
            $this->flashBag = new FlashBag($this);
        }
    }

    public function started(): bool
    {
        return $this->started;
    }

    public function flashBag(): FlashBag
    {
        if (!$this->flashBag) {
            $this->flashBag = new FlashBag($this);
        }
        return $this->flashBag;
    }

    public function has(string $key): bool
    {
        return array_key_exists($key, $this->data);
    }

    public function get(string $key, $default = null)
    {
        return $this->data[$key] ?? $default;
    }

    public function setMany(array $values): void
    {
        foreach ($values as $key => $value) {
            $this->set($key, $value);
        }
    }

    public function deleteCurrentDeviceSession(?int $userId = null): void
    {
        
    }

    public function set(string $key, $value): void
    {
        $this->data[$key] = $value;
    }

    public function forget(string $key): void
    {
        unset($this->data[$key]);
    }

    public function flush(): void
    {
        $this->data = [];
    }

    public function id(): string
    {
        return $this->id;
    }

    public function all(): array
    {
        return $this->data;
    }

    public function bag(string $key): SessionBag
    {
        return new SessionBag($key, $this);
    }

    abstract public function save(): void;

    public function gc(): void
    {
        
    }
}