<?php

namespace Framework\Database\Model\Relations;

use Framework\Database\Model\Entities\Entity;
use Framework\Database\Contracts\RepositoryInterface;

class MorphToMany extends Relation
{
    protected string $pivotTable;
    protected string $typeColumn;
    protected string $idColumn;
    protected string $pivotForeignKey;  // related_id
    protected string $relatedKey;
    protected string $morphType;

    protected array $eagerParentIds = [];

    public function __construct(
        Entity $parent,
        RepositoryInterface $repository,
        string $pivotTable,
        string $pivotForeignKey,
        string $typeColumn,
        string $idColumn,
        string $relatedKey = 'id'
    ) {
        parent::__construct($parent, $repository);

        $this->pivotTable = $pivotTable;
        $this->pivotForeignKey = $pivotForeignKey;
        $this->typeColumn = $typeColumn;
        $this->idColumn = $idColumn;
        $this->relatedKey = $relatedKey;
        $this->morphType = get_class($parent);
    }

    public function getResults(): array
    {
        $id = $this->parent->get($this->parent->getPrimaryKeyName());

        if (!$id) return [];

        return $this->repository
            ->join($this->pivotTable, $this->repository->getTable().'.'.$this->relatedKey, '=', $this->pivotTable.'.'.$this->pivotForeignKey)
            ->where($this->pivotTable.'.'.$this->typeColumn, $this->morphType)
            ->where($this->pivotTable.'.'.$this->idColumn, $id)
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
        $this->eagerParentIds = array_map(fn($e) => $e->get($e->getPrimaryKeyName()), $entities);
    }

    public function getEagerResults(array $entities): array
    {
        return $this->repository
            ->join($this->pivotTable, $this->repository->getTable().'.'.$this->relatedKey, '=', $this->pivotTable.'.'.$this->pivotForeignKey)
            ->where($this->pivotTable.'.'.$this->typeColumn, $this->morphType)
            ->whereIn($this->pivotTable.'.'.$this->idColumn, $this->eagerParentIds)
            ->get();
    }

    public function match(array $entities, array $results, string $relation): array
    {
        $dictionary = [];

        foreach ($results as $result) {
            $ownerId = $result->get($this->pivotTable.'.'.$this->idColumn);
            $dictionary[$ownerId][] = $result;
        }

        foreach ($entities as $entity) {
            $id = $entity->get($entity->getPrimaryKeyName());
            $entity->set($relation, $dictionary[$id] ?? []);
        }

        return $entities;
    }

    protected function getKeys(array $entities): array
    {
        return array_map(fn($e) => $e->get($e->getPrimaryKeyName()), $entities);
    }
}
