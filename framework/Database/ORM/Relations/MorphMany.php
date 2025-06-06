<?php

namespace Framework\Database\ORM\Relations;

use Framework\Database\ORM\Entities\Entity;
use Framework\Database\ORM\ORM;

/**
 * 다형성 1:N 관계 (ex: Post, User → images)
 * images 테이블: morph_type, morph_id 컬럼 필수
 */
class MorphMany extends Relation
{
    protected $morphType;
    protected $morphId;
    protected $localKey;
    protected $typeValue;

    public function __construct(
        Entity $parent,
        string $relatedEntityClass,
        string $morphType,
        string $morphId,
        string $localKey,
        string $typeValue = null
    ) {
        parent::__construct($parent, $relatedEntityClass);
        $this->morphType = $morphType;
        $this->morphId = $morphId;
        $this->localKey = $localKey;
        $this->typeValue = $typeValue ?: get_class($parent);
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
            $entity->{$relationName} = $grouped[$key] ?? [];
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
