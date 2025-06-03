<?php

namespace Framework\Database\Entities;

use Framework\Database\Contracts\CastInterface;
use Framework\Database\Contracts\EntityInterface;
use Framework\Database\Contracts\RepositoryInterface;
use Framework\Database\Factories\CastFactory;
use Framework\Database\Relations\{
    BelongsTo, BelongsToMany, HasMany, HasManyThrough,
    HasOne, HasOneThrough, MorphMany, MorphOne, MorphTo,
    MorphedByMany, MorphToMany
};
use Framework\Database\Persistence\Persistor;

abstract class Entity implements EntityInterface
{
    protected string $repository;
    protected array $attributes = [];
    protected array $original = [];
    protected array $casts = [];
    protected array $fillable = [];

    protected array $relations = [];


    public function __construct(array $attributes = [])
    {
        $this->fill($attributes);
        $this->syncOriginal();
    }

    public function fill(array $attributes): void
    {
        foreach ($attributes as $key => $value) {
            if (empty($this->fillable) || in_array($key, $this->fillable)) {
                $this->__set($key, $value);
            }
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
        foreach ($this->casts as $key => $cast) {
            if (is_string($cast)) {
                $this->casts[$key] = CastFactory::resolve($cast);
            }
        }
        return $this->casts;
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

        if ($cast instanceof CastInterface) {
            $value = $cast->recast($value);
        }

        $this->attributes[$key] = $value;
    }

    public function __get($key)
    {
        $value = $this->attributes[$key] ?? null;

        $cast = $this->getCasts()[$key] ?? null;

        if ($cast instanceof CastInterface) {
            $value = $cast->cast($value);
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

    public function isNew(): bool
    {
        return !$this->hasPrimaryKey();
    }

    public function save(): bool
    {
        return (new Persistor())->save($this);
    }

    public function delete(): bool
    {
        return (new Persistor())->delete($this);
    }

    public function getPrimaryKey()
    {
        return $this->get($this->getPrimaryKeyName());
    }

    public function repository(): RepositoryInterface
    {
        $class = $this->repositoryClass; 
        return app($class);
    }

    public function getPrimaryKeyName(): string
    {
        return $this->repository()->getPrimaryKeyName(); 
    }

    public function hasPrimaryKey(): bool
    {
        return !is_null($this->getPrimaryKey());
    }

    // ─── Relationship Helpers ─────────────────────

    protected function hasOne(
        string $relatedEntity,
        string $foreignKey,
        string $localKey = 'id'
    ): HasOne {
        return new HasOne(
            $this,
            $relatedEntity,
            $foreignKey,
            $localKey
        );
    }

    protected function hasMany(
        string $relatedEntity,
        string $foreignKey,
        string $localKey = 'id'
    ): HasMany {
        return new HasMany(
            $this,
            $relatedEntity,
            $foreignKey,
            $localKey
        );
    }

    protected function belongsTo(
        string $relatedEntity,
        string $foreignKey,
        string $ownerKey = 'id'
    ): BelongsTo {
        return new BelongsTo(
            $this,
            $relatedEntity,
            $foreignKey,
            $ownerKey
        );
    }

    protected function hasOneThrough(
        string $relatedEntity,
        string $throughEntity,
        string $firstKey,
        string $secondKey,
        string $localKey = 'id',
        string $secondLocalKey = 'id'
    ): HasOneThrough {
        return new HasOneThrough(
            $this,
            $relatedEntity,
            $throughEntity,
            $firstKey,
            $secondKey,
            $localKey,
            $secondLocalKey
        );
    }

    protected function hasManyThrough(
        string $relatedEntity,
        string $throughEntity,
        string $firstKey,
        string $secondKey,
        string $localKey = 'id',
        string $secondLocalKey = 'id'
    ): HasManyThrough {
        return new HasManyThrough(
            $this,
            $relatedEntity,
            $throughEntity,
            $firstKey,
            $secondKey,
            $localKey,
            $secondLocalKey
        );
    }

    protected function belongsToMany(
        string $relatedEntity,
        string $pivotTable,
        string $foreignPivotKey,
        string $relatedPivotKey,
        string $parentKey = 'id',
        string $relatedKey = 'id'
    ): BelongsToMany {
        return new BelongsToMany(
            $this,
            $relatedEntity,
            $pivotTable,
            $foreignPivotKey,
            $relatedPivotKey,
            $parentKey,
            $relatedKey
        );
    }

    protected function morphOne(
        string $relatedEntity,
        string $morphName,
        string $localKey = 'id'
    ): MorphOne {
        return new MorphOne(
            $this,
            $relatedEntity,
            $morphName,
            $localKey
        );
    }

    protected function morphMany(
        string $relatedEntity,
        string $morphName,
        string $localKey = 'id'
    ): MorphMany {
        return new MorphMany(
            $this,
            $relatedEntity,
            $morphName,
            $localKey
        );
    }

    protected function morphTo(
        string $morphName,
        string $ownerKey = 'id'
    ): MorphTo {
        return new MorphTo(
            $this,
            $morphName,
            $ownerKey
        );
    }

    protected function morphToMany(
        string $relatedEntity,
        string $pivotTable,
        string $foreignPivotKey,
        string $relatedPivotKey,
        string $morphName
    ): MorphToMany {
        return new MorphToMany(
            $this,
            $relatedEntity,
            $pivotTable,
            $foreignPivotKey,
            $relatedPivotKey,
            $morphName
        );
    }

    protected function morphedByMany(
        string $relatedEntity,
        string $pivotTable,
        string $foreignPivotKey,
        string $relatedPivotKey,
        string $morphName
    ): MorphedByMany {
        return new MorphedByMany(
            $this,
            $relatedEntity,
            $pivotTable,
            $foreignPivotKey,
            $relatedPivotKey,
            $morphName
        );
    }

}
