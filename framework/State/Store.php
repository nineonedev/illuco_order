<?php

namespace Framework\State;

class Store
{
    /**
     * @var array
     */
    protected $storage = [];

    public function put(string $key, $value): void
    {
        $this->storage[$key] = $value;
    }

    public function get(string $key, $default = null)
    {
        return $this->storage[$key] ?? $default;
    }

    public function has(string $key): bool
    {
        return array_key_exists($key, $this->storage);
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
}
