<?php

namespace Framework\Security\Session;

use Framework\Security\Session\Contracts\SessionInterface;

abstract class SessionStore implements SessionInterface
{
    protected string $id;
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
        $this->flashBag = new FlashBag($this);
    }

    public function flashBag(): FlashBag
    {
        if (!$this->flashBag) {
            $this->flashBag = new FlashBag($this);
        }
        return $this->flashBag;
    }

    public function hasFlashBag(): bool
    {
        return $this->flashBag !== null;
    }

    public function get(string $key, $default = null)
    {
        return $this->data[$key] ?? $default;
    }

    public function set(string $key, $value): void
    {
        $this->data[$key] = $value;
    }

    public function has(string $key): bool
    {
        return array_key_exists($key, $this->data);
    }

    public function forget(string $key): void
    {
        unset($this->data[$key]);
        // save() 호출 X
    }

    public function flush(): void
    {
        $this->data = [];
        // 바로 save() 가능, 또는 응답 직전에
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
}
