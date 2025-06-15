<?php

namespace Framework\State;

class Context
{
    /**
     * @var array<string, Store>
     */
    protected $stores = [];

    /**
     * @var string|null
     */
    protected $defaultStore = null;


    public function default(?string $storeName = null): Store
    {
        if ($storeName !== null && isset($this->stores[$storeName])) {
            return $this->stores[$storeName]; 
        }

        $this->ensureDefaultStore();
        return $this->stores[$this->defaultStore];
    }

    /**
     * Store를 등록합니다.
     */
    public function addStore(string $name, Store $store): void
    {
        $this->stores[$name] = $store;
        
        if (!$this->defaultStore) {
            $this->use($name);       
        }
    }

    /**
     * Store를 명시적으로 선택하여 사용합니다.
     */
    public function use(string $storeName): void
    {
        if (!isset($this->stores[$storeName])) {
            throw new \RuntimeException("Store [{$storeName}] has not been registered.");
        }

        $this->defaultStore = $storeName;
    }

    /**
     * 기본 store에 값을 저장합니다.
     */
    public function set(string $key, $value): void
    {
        $this->ensureDefaultStore();
        $this->stores[$this->defaultStore]->put($key, $value);
    }

    public function get(string $key, $default = null)
    {
        $this->ensureDefaultStore();
        return $this->stores[$this->defaultStore]->get($key, $default);
    }

    public function forget(string $key): void
    {
        if ($this->defaultStore !== null) {
            $this->stores[$this->defaultStore]->forget($key);
        }
    }

    public function has(string $key): bool
    {
        return $this->defaultStore !== null
            && $this->stores[$this->defaultStore]->has($key);
    }

    public function flush(string $storeName = null): void
    {
        if ($storeName !== null && isset($this->stores[$storeName])) {
            $this->stores[$storeName]->flush();
        } elseif ($this->defaultStore !== null) {
            $this->stores[$this->defaultStore]->flush();
        }
    }

    public function all(string $storeName = null): array
    {
        if ($storeName !== null && isset($this->stores[$storeName])) {
            return $this->stores[$storeName]->all();
        } elseif ($this->defaultStore !== null) {
            return $this->stores[$this->defaultStore]->all();
        }

        return [];
    }

    protected function ensureDefaultStore(): void
    {
        if ($this->defaultStore === null) {
            throw new \RuntimeException("Default store not set.");
        }
    }
}
