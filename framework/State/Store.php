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
        $segments = explode('.', $key);
        $ref = &$this->storage;

        foreach ($segments as $segment) {
            if (!isset($ref[$segment]) || !is_array($ref[$segment])) {
                $ref[$segment] = [];
            }
            $ref = &$ref[$segment];
        }

        $ref = $value;
    }



    public function get(string $key, $default = null)
    {
        $segments = explode('.', $key);
        $ref = $this->storage;

        foreach ($segments as $segment) {
            if (!is_array($ref) || !array_key_exists($segment, $ref)) {
                return $default;
            }
            $ref = $ref[$segment];
        }

        return $ref;
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
