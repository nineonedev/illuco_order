<?php

namespace Framework\Database\Model\Entities;

use Framework\Database\Contracts\CastInterface;
use Framework\Database\Model\Entities\Casts\CastFactory;

class Entity
{
    protected array $attributes = [];
    protected array $original = [];

    protected array $metadata = [];

    protected array $casts = [];

    protected array $fillable = [];

    protected array $guarded = [];

    protected array $relations = [];

    protected string $primaryKey = 'id';
    protected ?string $softDeleteColumn = 'deleted_at';
    

    public function __construct(array $attributes = [])
    {
        $this->casts = $this->defineCasts();
        $this->fill($attributes);
        $this->syncOriginal();
    }

    public function setRelation(string $name, $value): void
    {
        $this->relations[$name] = $value;
    }

    public function getRelation(string $name)
    {
        return $this->relations[$name] ?? null;
    }

    public function setMetadata(string $key, $value): void
    {
        $this->metadata[$key] = $value;
    }

    public function getMetadata(string $key, $default = null)
    {
        return $this->metadata[$key] ?? $default;
    }

    public function setGuarded(array $fields): void
    {
        $this->guarded = $fields;
    }

    public function getGuarded(): array
    {
        return $this->guarded;
    }

    protected function isFillable(string $key): bool
    {
        if (!empty($this->fillable)) {
            return in_array($key, $this->fillable, true);
        }

        return !in_array($key, $this->guarded, true);
    }

    public function setFillable(array $fields): void
    {
        $this->fillable = $fields;
    }

    public function getFillable(): array
    {
        return $this->fillable;
    }

    public function fill(array $attributes): void
    {
        foreach ($attributes as $key => $value) {
            if ($this->isFillable($key)) {
                $this->__set($key, $value);
            }
        }
    }

    public function __get($key)
    {
        $value = $this->attributes[$key] ?? null;

        if ($this->hasCast($key)) {
            return $this->getCast($key)->cast($value);
        }

        return $value;
    }

    public function __set($key, $value): void
    {
        if ($this->hasCast($key)) {
            $value = $this->getCast($key)->recast($value);
        }

        $this->attributes[$key] = $value;
    }


    protected function defineCasts(): array
    {
        return [];
    }

   protected function hasCast(string $key): bool
    {
        return isset($this->casts[$key]);
    }

    protected function getCast(string $key): CastInterface
    {
        return CastFactory::resolve($this->casts[$key]);
    }

    public function get(string $key)
    {
        return $this->__get($key);
    }

    public function set(string $key, $value): void
    {
        $this->__set($key, $value);
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

    public function setPrimaryKeyName(string $key): void
    {
        $this->primaryKey = $key;
    }

    public function getKey()
    {
        return $this->get($this->getPrimaryKeyName());
    }

    /**
     * 관계 선언
     */
    public function relations(): array
    {
        return [];
    }

    /**
     * SoftDelete 사용 여부 확인
     */
    public function usesSoftDeletes(): bool
    {
        return !empty($this->softDeleteColumn);
    }

    public function getSoftDeleteColumn(): ?string
    {
        return $this->softDeleteColumn;
    }

    /**
     * SoftDeleted 상태 여부
     */
    public function isSoftDeleted(): bool
    {
        if (!$this->usesSoftDeletes()) {
            return false;
        }

        return !empty($this->get($this->softDeleteColumn));
    }

    /**
     * 삭제 상태로 표시
     */
    public function markAsDeleted(): void
    {
        if ($this->usesSoftDeletes()) {
            $this->set($this->softDeleteColumn, date('Y-m-d H:i:s'));
        }
    }

    /**
     * Soft Delete 복구
     */
    public function restore(): void
    {
        if ($this->usesSoftDeletes()) {
            $this->set($this->softDeleteColumn, null);
        }
    }
}
