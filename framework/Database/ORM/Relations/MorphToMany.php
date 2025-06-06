<?php

namespace Framework\Database\ORM\Relations;

use Framework\Database\ORM\Entities\Entity;

/**
 * 다형성 N:N (ex: Post, Video → tags [taggables pivot])
 * pivot: taggables (taggable_type, taggable_id, tag_id)
 */
class MorphToMany extends Relation implements Pivotable
{
    protected $pivotTable;
    protected $morphType;         // taggable_type
    protected $morphId;           // taggable_id
    protected $pivotRelatedKey;   // tag_id
    protected $relatedEntityPrimaryKey;
    protected $typeValue;

    public function __construct(
        Entity $parent,
        string $relatedEntityClass,
        string $pivotTable,
        string $morphType,
        string $morphId,
        string $pivotRelatedKey,
        string $relatedEntityPrimaryKey = 'id',
        string $typeValue = null
    ) {
        parent::__construct($parent, $relatedEntityClass);
        $this->pivotTable = $pivotTable;
        $this->morphType = $morphType;
        $this->morphId = $morphId;
        $this->pivotRelatedKey = $pivotRelatedKey;
        $this->relatedEntityPrimaryKey = $relatedEntityPrimaryKey;
        $this->typeValue = $typeValue ?: get_class($parent);
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
            if ($morphId !== null) $grouped[$morphId][] = $item;
        }
        foreach ($entities as $entity) {
            $key = $entity->get($entity->getPrimaryKeyName());
            $entity->{$relationName} = $grouped[$key] ?? [];
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
