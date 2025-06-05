<?php

namespace Framework\Database\Model\Relations;

use Framework\Database\Model\Entities\Entity;
use Framework\Database\Query\Builder;

class PivotManager
{
    protected string $table;
    protected string $foreignKey;
    protected string $relatedKey;

    public function __construct(string $table, string $foreignKey, string $relatedKey)
    {
        $this->table = $table;
        $this->foreignKey = $foreignKey;
        $this->relatedKey = $relatedKey;
    }

    protected function query(): Builder
    {
        return db($this->table);
    }

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

    public function sync(Entity $parent, array $relatedData): void
    {
        // $relatedData = [related_id => [extra_attrs], ...]
        $existingIds = $this->query()
            ->where($this->foreignKey, $parent->get($parent->getPrimaryKeyName()))
            ->pluck($this->relatedKey);

        $detachIds = array_diff($existingIds, array_keys($relatedData));
        if (!empty($detachIds)) {
            $this->detach($parent, $detachIds);
        }

        foreach ($relatedData as $relatedId => $attrs) {
            $this->query()->insertOrIgnore([
                $this->foreignKey => $parent->get($parent->getPrimaryKeyName()),
                $this->relatedKey => $relatedId,
                ...$attrs
            ]);
        }
    }
}
