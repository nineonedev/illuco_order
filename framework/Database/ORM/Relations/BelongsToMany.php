<?php

namespace Framework\Database\ORM\Relations;

use Framework\Database\ORM\Entities\Entity;
use Framework\Database\Query\Builder;
use Framework\Database\Query\EntityQueryBuilder;
use Framework\Support\Facades\DB;

class BelongsToMany extends Relation implements Pivotable
{
    protected string $pivotTable;
    protected string $foreignKey;
    protected string $relatedKey;
    protected string $relatedEntityPrimaryKey;

    public function __construct(
        Entity $parent,
        string $relatedEntityClass,
        string $pivotTable,
        string $foreignKey,
        string $relatedKey,
        string $relatedEntityPrimaryKey = 'id'
    ) {
        parent::__construct($parent, $relatedEntityClass);

        $this->pivotTable = $pivotTable;
        $this->foreignKey = $foreignKey;
        $this->relatedKey = $relatedKey;
        $this->relatedEntityPrimaryKey = $relatedEntityPrimaryKey;
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
                "{$this->pivotTable}.{$this->relatedKey}"
            )
            ->whereColumn(
                "{$this->pivotTable}.{$this->foreignKey}",
                '=',
                $parentQuery->getTable() . '.' . $this->parent->getPrimaryKeyName()
            );
    }


    public function addEagerConstraints(array $entities): void
    {
        $parentKeys = array_map(function ($entity) {
            return $entity->get($entity->getPrimaryKeyName());
        }, $entities);

        $relatedTable = $this->query->getTable();

        $this->query
            ->select(array_merge(["{$relatedTable}.*"], $this->aliasedPivotColumns()))
            ->join(
                $this->pivotTable,
                "{$relatedTable}.{$this->relatedEntityPrimaryKey}",
                '=',
                "{$this->pivotTable}.{$this->relatedKey}"
            )
            ->whereIn("{$this->pivotTable}.{$this->foreignKey}", $parentKeys);
    }

    public function getEagerResults(array $entities): array
    {
        $rawResults = $this->query->get();

        return array_map(function ($row) {
            if ($row instanceof Entity) return $row;
            return $this->makeEntity((array)$row); 
        }, $rawResults);
    }

    public function match(array $entities, array $results, string $relationName): void
    {
        $grouped = [];

        foreach ($results as $item) {
            $pivot = $item->__get('pivot');
            $pivotFk = $pivot ? $pivot->get($this->foreignKey) : null;

            if ($pivotFk !== null) {
                $grouped[$pivotFk][] = $item;
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

    public function getResults(): array
    {
        $parentKey = $this->parent->get($this->parent->getPrimaryKeyName());
        $relatedTable = $this->query->getTable();

        return $this->query
            ->select(array_merge(["{$relatedTable}.*"], $this->aliasedPivotColumns()))
            ->join(
                $this->pivotTable,
                "{$relatedTable}.{$this->relatedEntityPrimaryKey}",
                '=',
                "{$this->pivotTable}.{$this->relatedKey}"
            )
            ->where("{$this->pivotTable}.{$this->foreignKey}", $parentKey)
            ->get();
    }

    public function attach(Entity $related, array $attributes = []): bool
    {
        $pivot = new Pivot();
        $pivot->setTable($this->pivotTable);
        $pivot->setPivotKeys($this->foreignKey, $this->relatedKey);

        return $pivot->attach($this->parent, $related, $attributes);
    }

    public function detach(array $relatedIds = []): int
    {
        $pivot = new Pivot();
        $pivot->setTable($this->pivotTable);
        $pivot->setPivotKeys($this->foreignKey, $this->relatedKey);

        return $pivot->detach($this->parent, $relatedIds);
    }

    public function sync(array $relatedData): void
    {
        $pivot = new Pivot();
        $pivot->setTable($this->pivotTable);
        $pivot->setPivotKeys($this->foreignKey, $this->relatedKey);

        $pivot->sync($this->parent, $relatedData);
    }

    protected function aliasedPivotColumns(): array
    {
        return array_map(function ($column) {
            return "{$this->pivotTable}.{$column} as pivot_{$column}";
        }, $this->getPivotColumns());
    }

    protected function getPivotColumns(): array
    {
        return DB::schema()->getColumnListing($this->pivotTable);
    }
}
