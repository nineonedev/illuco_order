<?php

namespace Framework\Database\ORM\Relations;

use Framework\Database\ORM\Entities\Entity;
use Framework\Database\Query\Builder;
use Framework\Database\ORM\Casts\CastFactory;

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

    // ----- Core -----

    protected function query(): Builder
    {
        return db($this->table);
    }

    public static function fromAttributes(Entity $parent, array $attributes, string $table): self
    {
        $pivot = new static($attributes);
        $pivot->setPivotParent($parent);
        $pivot->setTable($table);
        return $pivot;
    }

    // ----- Data Access -----

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

    // ----- Timestamp Helpers -----

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

    // ----- Entity Reference -----

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

    // ----- Table/Key -----

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

    // ----- CRUD (Attach/Detach/Sync) -----

    public function attach(Entity $parent, Entity $related, array $attributes = []): bool
    {
        $data = array_merge($attributes, [
            $this->foreignKey => $parent->get($parent->getPrimaryKeyName()),
            $this->relatedKey => $related->get($related->getPrimaryKeyName()),
        ]);
        return $this->query()->insert($data);
    }

    public function detach(Entity $parent, array $relatedIds = []): int
    {
        $query = $this->query()->where($this->foreignKey, $parent->get($parent->getPrimaryKeyName()));
        if (!empty($relatedIds)) {
            $query->whereIn($this->relatedKey, $relatedIds);
        }
        return $query->delete();
    }

    /**
     * @param Entity $parent
     * @param array $relatedData [related_id => [extra_attrs], ...] 또는 [1, 2, 3]
     */
    public function sync(Entity $parent, array $relatedData): void
    {
        $relatedIds = array_keys($relatedData);

        $existingIds = (array) $this->query()
            ->where($this->foreignKey, $parent->get($parent->getPrimaryKeyName()))
            ->pluck($this->relatedKey);

        // 먼저 detach (불필요한 항목 제거)
        $detachIds = array_diff($existingIds, $relatedIds);

        if (!empty($detachIds)) {
            $this->detach($parent, $detachIds);
        }

        // 필요한 항목만 attach or insert
        foreach ($relatedData as $relatedId => $extra) {
            $extra = is_array($extra) ? $extra : [];

            $this->query()->insertOrIgnore([
                array_merge([
                    $this->foreignKey => $parent->get($parent->getPrimaryKeyName()),
                    $this->relatedKey => $relatedId,
                ], $extra instanceof Entity ? $extra->toArray() : $extra)
            ]);
        }
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
