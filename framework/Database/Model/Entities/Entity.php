<?php

namespace Framework\Database\Model\Entities;

use Framework\Database\Model\Casts\CastFactory;
use Framework\Database\Model\Casts\CastInterface;
use RuntimeException;

abstract class Entity
{
    protected array $attributes = [];
    protected array $original = [];
    protected array $fillable = [];
    protected array $guarded = [];
    protected array $casts = [];
    protected array $relations = [];
    protected string $primaryKey = 'id';

    protected ?string $softDeleteColumn = null;
    protected ?SoftDeletes $softDeletes = null;

    public function __construct(array $attributes = [])
    {
        $this->setup();

        $this->fill($attributes);
        $this->syncOriginal();

    }

    protected function setup(): void
    {
        if ($this->softDeleteColumn) {
            if (!in_array($this->softDeleteColumn, $this->fillable)) {
                $this->fillable[] = $this->softDeleteColumn;
            }

            $this->softDeletes = new SoftDeletes($this, $this->softDeleteColumn);
        }
    }

    public function setRelation(string $name, $value): void
    {
        $this->relations[$name] = $value;
    }

    public function getRelation(string $name)
    {
        return $this->relations[$name] ?? null;
    }
    
    public function getRelations(): array
    {
        return $this->relations;
    }

    public function relationLoaded(string $relation): bool
    {
        return array_key_exists($relation, $this->relations);
    }

    public static function create(array $attributes = []): self
    {
        return new static($attributes);
    }

    public function fill(array $attributes): void
    {
        foreach ($attributes as $key => $value) {
            if ($this->isGuarded($key)) {
                continue;
            }

            if (!empty($this->fillable) && !in_array($key, $this->fillable, true)) {
                continue;
            }

            $this->set($key, $value);
        }
    }

    public function set(string $key, $value): void
    {
        if ($cast = $this->resolveCast($key)) {
            $this->attributes[$key] = $cast->set($value);
        } else {
            $this->attributes[$key] = $value;
        }
    }

    public function get(string $key)
    {
        $value = $this->attributes[$key] ?? null;

        if ($cast = $this->resolveCast($key)) {
            return $cast->get($value);
        }

        return $value;
    }

    public function toArray(): array
    {
        $result = [];

        foreach (array_keys($this->attributes) as $key) {
            $result[$key] = $this->get($key);
        }

        return $result;
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }

    public function syncOriginal(): void
    {
        $this->original = $this->attributes;
    }

    public function getChanges(): array
    {
        return array_diff_assoc($this->attributes, $this->original);
    }

    public function isClean(): bool
    {
        return $this->getChanges() === [];
    }

    public function isDirty(): bool
    {
        return $this->getChanges() !== [];
    }

    public function hasPrimaryKey(): bool
    {
        $key = $this->getPrimaryKeyName();
        return isset($this->attributes[$key]) && !empty($this->attributes[$key]);
    }

    public function getPrimaryKey()
    {
        return $this->get($this->getPrimaryKeyName());
    }

    public function getPrimaryKeyName(): string
    {
        return $this->primaryKey;
    }

    protected function resolveCast(string $key): ?CastInterface
    {
        if (!isset($this->casts[$key])) {
            return null;
        }

        $cast = $this->casts[$key];

        // 문자열 타입인 경우 CastFactory 사용
        if (is_string($cast)) {
            return CastFactory::resolve($cast);
        }

        if (is_object($cast) && $cast instanceof CastInterface) {
            return $cast;
        }

        if (class_exists($cast)) {
            $instance = new $cast();
            if (!$instance instanceof CastInterface) {
                throw new RuntimeException("Cast class [$cast] must implement CastInterface.");
            }
            return $instance;
        }

        throw new RuntimeException("Invalid cast definition for [$key].");
    }

    protected function isGuarded(string $key): bool
    {
        return in_array($key, $this->guarded, true);
    }

    // =======================================
    // Soft delete methods
    // =======================================
    public function usesSoftDeletes(): bool
    {
        return $this->softDeleteColumn !== null; 
    }

    public function softDeleteColumn(): ?string
    {
        return $this->softDeleteColumn; 
    }

    public function markDeleted(): void
    {
        if (!$this->usesSoftDeletes()) return; 

        $this->softDeletes->markDeleted();
    }

    public function restore(): void
    {
        if (!$this->usesSoftDeletes()) return; 

        $this->softDeletes->restore();
    }

    public function isDeleted(): bool
    {
        if (!$this->usesSoftDeletes()) return false; 

        return $this->softDeletes->isDeleted();
    }

    public function deletedAt(): ?string
    {
        if (!$this->usesSoftDeletes()) return null;
        
        return $this->softDeletes->deletedAt();
    }
}
