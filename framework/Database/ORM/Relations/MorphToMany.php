<?php

namespace Framework\Database\ORM\Relations;

use Framework\Database\ORM\Entities\Entity;
use Framework\Database\ORM\RelationMap;

class MorphToMany extends MorphRelation implements Pivotable
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
        $this->morphType = $morphType;
        $this->morphId = $morphId;
        $this->pivotRelatedKey = $pivotRelatedKey;
        $this->relatedEntityPrimaryKey = $relatedEntityPrimaryKey;

        // MorphMap 별칭 우선, 없으면 클래스명
        $this->typeValue = $typeValue
            ?: (RelationMap::morphAlias(get_class($parent)) ?? get_class($parent)::alias());
    }

    public function addEagerConstraints(array $entities): void
    {
        $parentKey = $this->parent->getPrimaryKeyName();
        $parentIds = array_map(fn($e) => $e->get($parentKey), $entities);

        $this->query
            ->join(
                $this->pivotTable,
                "{$this->relatedEntityClass::table()}.{$this->relatedEntityPrimaryKey}",
                '=',
                "{$this->pivotTable}.{$this->pivotRelatedKey}"
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
            // Pivot 객체에서 morph_id 가져오기
            $morphId = $item->{$this->pivotTable}[$this->morphId] ?? null;
            if ($morphId !== null) $grouped[$morphId][] = $item;
        }
        foreach ($entities as $entity) {
            $key = $entity->get($entity->getPrimaryKeyName());
            $entity->setRelation($relationName, $grouped[$key] ?? []);
        }
    }

    public function getResults()
    {
        $parentId = $this->parent->get($this->parent->getPrimaryKeyName());

        return $this->query
            ->join(
                $this->pivotTable,
                "{$this->relatedEntityClass::table()}.{$this->relatedEntityPrimaryKey}",
                '=',
                "{$this->pivotTable}.{$this->pivotRelatedKey}"
            )
            ->where("{$this->pivotTable}.{$this->morphType}", $this->typeValue)
            ->where("{$this->pivotTable}.{$this->morphId}", $parentId)
            ->get();
    }

    // ----- Pivot attach/detach/sync -----
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
