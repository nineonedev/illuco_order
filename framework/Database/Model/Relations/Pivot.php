<?php

namespace Framework\Database\Model\Relations;

use Framework\Database\Model\Casts\CastFactory;
use Framework\Database\Model\Entities\Entity;

class Pivot
{
    protected array $attributes = [];
    protected array $casts = [];

    protected ?Entity $pivotParent = null;
    protected ?Entity $pivotRelated = null;

    protected ?string $table = null;
    protected ?string $foreignKey = null;
    protected ?string $relatedKey = null;

    public function __construct(array $attributes = [])
    {
        $this->fill($attributes);
    }

    public static function fromAttributes(Entity $parent, array $attributes, string $table): self
    {
        $pivot = new static($attributes);
        $pivot->setPivotParent($parent);
        $pivot->setTable($table);
        return $pivot;
    }

    public function setCasts(array $casts): self
    {
        $this->casts = $casts;
        return $this;
    }

    public function get(string $key)
    {
        $value = $this->attributes[$key] ?? null;

        if (isset($this->casts[$key])) {
            return CastFactory::resolve($this->casts[$key])->get($value);
        }

        return $value;
    }

    public function set(string $key, $value): void
    {
        if (isset($this->casts[$key])) {
            $value = CastFactory::resolve($this->casts[$key])->set($value);
        }

        $this->attributes[$key] = $value;
    }

    public function fill(array $data): void
    {
        foreach ($data as $key => $value) {
            $this->set($key, $value);
        }
    }

    public function all(): array
    {
        return $this->attributes;
    }

    public function has(string $key): bool
    {
        return array_key_exists($key, $this->attributes);
    }

    public function hasTimestamps(): bool
    {
        return isset($this->attributes['created_at']) || isset($this->attributes['updated_at']);
    }

    public function getCreatedAt(): ?string
    {
        return $this->get('created_at');
    }

    public function getUpdatedAt(): ?string
    {
        return $this->get('updated_at');
    }

    public function setPivotParent(Entity $entity): void
    {
        $this->pivotParent = $entity;
    }

    public function getPivotParent(): ?Entity
    {
        return $this->pivotParent;
    }

    public function setPivotRelated(Entity $entity): void
    {
        $this->pivotRelated = $entity;
    }

    public function getPivotRelated(): ?Entity
    {
        return $this->pivotRelated;
    }

    public function setTable(string $table): void
    {
        $this->table = $table;
    }

    public function getTable(): ?string
    {
        return $this->table;
    }

    public function setPivotKeys(string $foreignKey, string $relatedKey): void
    {
        $this->foreignKey = $foreignKey;
        $this->relatedKey = $relatedKey;
    }

    public function getForeignKey(): ?string
    {
        return $this->foreignKey;
    }

    public function getRelatedKey(): ?string
    {
        return $this->relatedKey;
    }

    public function toArray(): array
    {
        $result = [];

        foreach ($this->attributes as $key => $value) {
            $result[$key] = $this->get($key);
        }

        return $result;
    }
}
