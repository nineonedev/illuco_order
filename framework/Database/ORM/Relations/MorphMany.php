<?php

namespace Framework\Database\ORM\Relations;

use Framework\Database\ORM\Entities\Entity;
use Framework\Database\ORM\RelationMap;

class MorphMany extends Relation
{
    protected $morphType;
    protected $morphId;
    protected $localKey;
    protected $typeValue;

    public function __construct(
        Entity $parent,
        string $relatedEntityClass,
        string $morphType = 'morph_type',
        string $morphId = 'morph_id',
        string $localKey = 'id',
        $typeValue = null
    ) {
        parent::__construct($parent, $relatedEntityClass);
        $this->morphType = $morphType;
        $this->morphId = $morphId;
        $this->localKey = $localKey;

        // typeValue 미지정시 morphMap 별칭 → 클래스명 순
        $this->typeValue = $typeValue
            ?: (RelationMap::morphAlias(get_class($parent)) ?? get_class($parent));
    }

    public function addEagerConstraints(array $entities): void
    {
        $ids = array_map(fn($e) => $e->get($this->localKey), $entities);

        $this->query = $this->query
            ->whereIn($this->morphId, $ids)
            ->where($this->morphType, $this->typeValue);
    }

    public function getEagerResults(array $entities): array
    {
        if (!$this->query) return [];
        return $this->query->get();
    }

    public function match(array $entities, array $results, string $relationName): void
    {
        $grouped = [];
        foreach ($results as $item) {
            $id = $item->get($this->morphId);
            $grouped[$id][] = $item;
        }
        foreach ($entities as $entity) {
            $key = $entity->get($this->localKey);
            $entity->setRelation($relationName, $grouped[$key] ?? []);
        }
    }

    public function getResults()
    {
        $id = $this->parent->get($this->localKey);

        return $this->query
            ->where($this->morphId, $id)
            ->where($this->morphType, $this->typeValue)
            ->get();
    }
}
