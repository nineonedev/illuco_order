<?php

namespace Framework\Security\Session\Bags;

use Framework\Security\Session\SessionStore;

class SessionBag
{
    protected string $name;
    protected SessionStore $store;

    public function __construct(string $name, SessionStore $store)
    {
        $this->name = $name;
        $this->store = $store;
    }

    public function all(): array
    {
        return (array) $this->store->get($this->name, []);
    }

    public function get(string $key, $default = null)
    {
        $items = $this->all();
        return $items[$key] ?? $default;
    }

    public function set(string $key, $value): void
    {
        $items = $this->all();
        $items[$key] = $value;
        $this->store->set($this->name, $items);
    }

    public function has(string $key): bool
    {
        $items = $this->all();
        return array_key_exists($key, $items);
    }

    public function forget(string $key): void
    {
        $items = $this->all();
        unset($items[$key]);
        $this->store->set($this->name, $items);
    }

    public function flush(): void
    {
        $this->store->forget($this->name);
    }
}