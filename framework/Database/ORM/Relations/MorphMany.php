<?php

namespace Framework\Database\ORM\Relations;

use Framework\Database\ORM\Entities\Entity;

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
        $this->typeValue = $typeValue ?? get_class($parent)::alias();
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
        $id = $this->parent->get($this->localKey);

        return $this->query
            ->where($this->morphId, $id)
            ->where($this->morphType, $this->typeValue)
            ->get();
    }
}
