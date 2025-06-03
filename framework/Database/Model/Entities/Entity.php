<?php

namespace Framework\Database\Model\Entities;

abstract class Entity
{
    protected array $attributes = [];
    protected array $original = [];
    protected string $primaryKey = 'id'; 

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

    public function get(string $key)
    {
        return $this->__get($key);
    }

    public function set(string $key, $value): void
    {
        $this->__set($key, $value);
    }

    public function __get($key)
    {
        return $this->attributes[$key] ?? null;
    }

    public function __set($key, $value): void
    {
        $this->attributes[$key] = $value;
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }

    public function getOriginal(): array
    {
        return $this->original;
    }

    public function syncOriginal(): void
    {
        $this->original = $this->attributes;
    }

    public function isDirty(?string $key = null): bool
    {
        return $key
            ? ($this->attributes[$key] ?? null) !== ($this->original[$key] ?? null)
            : count($this->getChanges()) > 0;
    }

    public function getChanges(): array
    {
        $changes = [];

        foreach ($this->attributes as $key => $value) {
            if (!array_key_exists($key, $this->original) || $value !== $this->original[$key]) {
                $changes[$key] = $value;
            }
        }

        return $changes;
    }

    public function hasPrimaryKey(): bool
    {
        $key = $this->getPrimaryKeyName();
        return !empty($this->get($key));
    }

    public function getPrimaryKeyName(): string
    {
        return $this->primaryKey;
    }

    /**
     * 관계 메타 선언 (BelongsTo, HasMany 등에서 사용)
     */
    public function relations(): array
    {
        return [];
    }
}
