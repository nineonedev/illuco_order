<?php

namespace Framework\Database\Entities;

use Framework\Database\Contracts\EntityInterface;

abstract class Entity implements EntityInterface
{
    protected array $attributes = []; 
    protected array $original = []; 
    protected array $casts = [];

    public function __construct(array $attributes = [])
    {
        $this->fill($attributes); 
        $this->syncOriginal(); 
    }

    public function fill(array $attributes): void
    {
        foreach ($attributes as $key => $value) {
            $this->__set($key, $value); 
        }
    }

    public function syncOriginal(): void
    {
        $this->original = $this->attributes; 
    }

    public function getAttributes(): array
    {
        return $this->attributes; 
    }

    public function getOriginal(): array
    {
        return $this->original; 
    }

    public function getCasts(): array
    {
        if (empty($this->casts)) {
            return []; 
        }

        foreach ($this->casts as $key => $cast) {
            if (is_string($cast)) {
                $this->casts[$key] = $this->resolveCastFromString($cast); 
            }
        }

        return $this->casts; 
    }

    protected function resolveCastFromString(string $type): Attribute
    {
        switch ($type) {
            case 'bool':
                return Attribute::cast(fn ($v) => (bool) $v); 
            case 'int':
                return Attribute::cast(fn ($v) => (int) $v);
            case 'float':
                return Attribute::cast(fn ($v) => (float) $v);
            case 'string':
                return Attribute::cast(fn ($v) => (string) $v); 
            case 'array':
                return new Attribute(
                    fn ($v) => is_string($v) ? json_decode($v, true) : $v,
                    fn ($v) => json_encode($v)
                );
            case 'datetime':
                return new Attribute(
                    fn ($v) => $v ? new \DateTime($v) : null,
                    fn ($v) => $v instanceof \DateTime ? $v->format('Y-m-d H:i:s') : $v
                ); 
            default:
                throw new \InvalidArgumentException("Unsupported cast type [$type]");
        }
    }

    public function isDirty(?string $key = null): bool
    {
        return $key 
            ? ($this->attributes[$key] ?? null) !== ($this->original[$key] ?? null)
            : count($this->getChanges()) > 0; 
    }

    public function isClean(?string $key = null): bool
    {
        return !$this->isDirty($key); 
    }

    public function getChanges(): array
    {
        $changes = []; 

        foreach ($this->attributes as $key => $value) {
            if (!array_key_exists($key, $this->original)) {
                $changes[$key] = $value; 
            } elseif ($value !== $this->original[$key]) {
                $changes[$key] = $value; 
            }
        }

        return $changes; 
    }

    public function get(string $key)
    {
        return $this->__get($key);
    }

    public function set(string $key, $value): void
    {
        $this->__set($key, $value); 
    }

    public function __set($key, $value): void
    {
        $cast = $this->getCasts()[$key] ?? null; 

        if ($cast instanceof Attribute) {
            $value = $cast->set($value); 
        }

        $this->attributes[$key] = $value; 
    }

    public function __get($key)
    {
        $value = $this->attributes[$key] ?? null; 

        $cast = $this->getCasts()[$key] ?? null; 

        if ($cast instanceof Attribute) {
            $value = $cast->get($value); 
        }

        return $value; 
    }

    public function __isset($key): bool
    {
        return isset($this->attributes[$key]);
    }

    public function __unset($key): void
    {
        unset($this->attributes[$key]);
    }
}