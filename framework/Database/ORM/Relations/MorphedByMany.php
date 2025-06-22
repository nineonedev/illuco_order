<?php

namespace Framework\Database\ORM\Relations;

use Framework\Database\ORM\Entities\Entity;
use Framework\Database\ORM\RelationMap;
use Framework\Database\Query\Builder;

class MorphedByMany extends Relation implements Pivotable
{
    protected $pivotTable;
    protected $morphType;
    protected $morphId;
    protected $pivotRelatedKey;
    protected $relatedEntityPrimaryKey;
    protected $typeValue;

    public function __construct(
        Entity $parent,
        string $relatedEntityClass,
        string $pivotTable,
        string $pivotRelatedKey,
        string $morphType = 'morph_type',
        string $morphId = 'morph_id',
        string $relatedEntityPrimaryKey = 'id',
        $typeValue = null
    ) {
        parent::__construct($parent, $relatedEntityClass);
        $this->pivotTable = $pivotTable;
        $this->pivotRelatedKey = $pivotRelatedKey;
        $this->morphType = $morphType;
        $this->morphId = $morphId;
        $this->relatedEntityPrimaryKey = $relatedEntityPrimaryKey;
        $this->typeValue = $typeValue ?? get_class($parent)::alias();
    }

    public function getRelatedQuery(): Builder
    {
        return $this->query;
    }

    public function addExistsConstraints(Builder $relatedQuery, Builder $parentQuery): void
    {
        $relatedTable = $relatedQuery->getTable();

        $relatedQuery
            ->join(
                $this->pivotTable,
                "{$relatedTable}.{$this->relatedEntityPrimaryKey}",
                '=',
                "{$this->pivotTable}.{$this->pivotRelatedKey}"
            )
            ->whereColumn(
                "{$this->pivotTable}.{$this->morphId}",
                '=',
                $parentQuery->getTable() . '.' . $this->parent->getPrimaryKeyName()
            )
            ->where("{$this->pivotTable}.{$this->morphType}", $this->typeValue);
    }


    public function addEagerConstraints(array $entities): void
    {
        $parentIds = array_map(fn($e) => $e->get($e->getPrimaryKeyName()), $entities);

        $this->query
            ->join(
                $this->pivotTable,
                "{$this->relatedEntityClass::table()}.{$this->relatedEntityPrimaryKey}", '=', "{$this->pivotTable}.{$this->pivotRelatedKey}"
            )
            ->where("{$this->pivotTable}.{$this->morphType}", $this->typeValue)
            ->whereIn("{$this->pivotTable}.{$this->morphId}", $parentIds);
    }

    public function getEagerResults(array $entities): array
    {
        if (!$this->query) return [];
        return $this->query->get();
    }

    public function match(array $entities, array $results, string $relationName): void
    {
        // pivot의 morph_id 기준으로 그룹핑 (각 부모별 여러개)
        $grouped = [];
        foreach ($results as $item) {
            $morphId = $item->{$this->pivotTable}[$this->morphId] ?? null;
            if ($morphId !== null) {
                $grouped[$morphId][] = $item;
            }
        }

        foreach ($entities as $entity) {
            $key = $entity->get($entity->getPrimaryKeyName());
            $existing = $entity->getRelation($relationName);
            $current = $grouped[$key] ?? [];

            if (is_array($existing)) {
                $entity->setRelation($relationName, array_merge($existing, $current));
            } else {
                $entity->setRelation($relationName, $current);
            }
        }
    }

    public function getResults()
    {
        $parentId = $this->parent->get($this->parent->getPrimaryKeyName());

        return $this->query
            ->join(
                $this->pivotTable,
                "{$this->relatedEntityClass::table()}.{$this->relatedEntityPrimaryKey}", '=', "{$this->pivotTable}.{$this->pivotRelatedKey}"
            )
            ->where("{$this->pivotTable}.{$this->morphType}", $this->typeValue)
            ->where("{$this->pivotTable}.{$this->morphId}", $parentId)
            ->get();
    }

    // ----- Pivot attach/detach/sync도 제공 (BelongsToMany 참고)
    public function attach(Entity $related, array $attributes = []): bool
    {
        $pivot = new Pivot();
        $pivot->setTable($this->pivotTable);
        $pivot->setPivotKeys($this->morphId, $this->pivotRelatedKey);
        return $pivot->attach($this->parent, $related, array_merge(
            $attributes,
            [$this->morphType => $this->typeValue]
        ));
    }

    public function detach(array $relatedIds = []): int
    {
        $pivot = new Pivot();
        $pivot->setTable($this->pivotTable);
        $pivot->setPivotKeys($this->morphId, $this->pivotRelatedKey);
        return $pivot->detach($this->parent, $relatedIds);
    }

    public function sync(array $relatedData): void
    {
        $pivot = new Pivot();
        $pivot->setTable($this->pivotTable);
        $pivot->setPivotKeys($this->morphId, $this->pivotRelatedKey);
        $pivot->sync($this->parent, $relatedData);
    }
}
