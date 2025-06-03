<?php

namespace Framework\Database\Model\Relations;

use Framework\Database\Model\Entities\Entity;
use Framework\Database\Contracts\RepositoryInterface;
use Framework\Support\Collection;

class BelongsToMany extends Relation
{
    protected string $pivotTable;
    protected string $foreignPivotKey;
    protected string $relatedPivotKey;
    protected string $parentKey;
    protected string $relatedKey;

    protected array $eagerParentIds = [];

    public function __construct(
        Entity $parent,
        RepositoryInterface $repository,
        string $pivotTable,
        string $foreignPivotKey,
        string $relatedPivotKey,
        string $parentKey = 'id',
        string $relatedKey = 'id'
    ) {
        parent::__construct($parent, $repository);
        $this->pivotTable = $pivotTable;
        $this->foreignPivotKey = $foreignPivotKey;
        $this->relatedPivotKey = $relatedPivotKey;
        $this->parentKey = $parentKey;
        $this->relatedKey = $relatedKey;
    }

    public function getResults(): array
    {
        $parentId = $this->parent->get($this->parentKey);

        if (!$parentId) {
            return [];
        }

        return $this->repository
            ->query()
            ->join($this->pivotTable, $this->relatedKey, '=', $this->pivotTable.'.'.$this->relatedPivotKey)
            ->where($this->pivotTable.'.'.$this->foreignPivotKey, $parentId)
            ->get();
    }

    public function initRelation(array $entities, string $relation): array
    {
        foreach ($entities as $entity) {
            $entity->set($relation, []);
        }

        return $entities;
    }

    public function addEagerConstraints(array $entities): void
    {
        $this->eagerParentIds = array_unique(array_filter($this->getKeys($entities)));
    }

    public function getEagerResults(array $entities): array
    {
        if (empty($this->eagerParentIds)) {
            return [];
        }

        return $this->repository
            ->query()
            ->join($this->pivotTable, $this->relatedKey, '=', $this->pivotTable.'.'.$this->relatedPivotKey)
            ->whereIn($this->pivotTable.'.'.$this->foreignPivotKey, $this->eagerParentIds)
            ->get();
    }

    public function match(array $entities, array $results, string $relation): array
    {
        $dictionary = [];

        foreach ($results as $result) {
            $parentId = $result->get($this->pivotTable.'.'.$this->foreignPivotKey);
            $dictionary[$parentId][] = $result;
        }

        foreach ($entities as $entity) {
            $id = $entity->get($this->parentKey);
            $entity->set($relation, $dictionary[$id] ?? []);
        }

        return $entities;
    }

    protected function getKeys(array $entities): array
    {
        return array_map(fn($entity) => $entity->get($this->parentKey), $entities);
    }
}
