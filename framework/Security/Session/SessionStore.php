<?php

namespace Framework\Security\Session;

use Framework\Security\Session\Contracts\SessionInterface;


abstract class SessionStore implements SessionInterface
{
    protected string $id;
    protected array $data = [];
    protected bool $started = false;

    public function get(string $key, $default = null)
    {
        return $this->data[$key] ?? $default;
    }

    public function set(string $key, $value): void
    {
        $this->data[$key] = $value;
        $this->save();
    }

    public function has(string $key): bool
    {
        return array_key_exists($key, $this->data);
    }

    public function forget(string $key): void
    {
        unset($this->data[$key]);
        $this->save();
    }

    public function flush(): void
    {
        $this->data = [];
        $this->save();
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


    abstract protected function save(): void;
    
}
