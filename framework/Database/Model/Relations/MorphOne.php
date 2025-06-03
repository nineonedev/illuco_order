<?php

namespace Framework\Database\Model\Relations;

use Framework\Database\Model\Entities\Entity;
use Framework\Database\Contracts\RepositoryInterface;

class MorphOne extends Relation
{
    protected string $typeColumn;
    protected string $idColumn;
    protected string $typeValue;

    protected array $eagerConstraints = [];

    public function __construct(
        Entity $parent,
        RepositoryInterface $repository,
        string $typeColumn,
        string $idColumn
    ) {
        parent::__construct($parent, $repository);

        $this->typeColumn = $typeColumn;
        $this->idColumn = $idColumn;
        $this->typeValue = get_class($parent);
    }

    public function getResults(): ?Entity
    {
        $id = $this->parent->get($this->parent->getPrimaryKeyName());

        if (!$id) {
            return null;
        }

        $results = $this->repository
            ->where($this->typeColumn, $this->typeValue)
            ->where($this->idColumn, $id);

        return $results[0] ?? null;
    }

    public function initRelation(array $entities, string $relation): array
    {
        foreach ($entities as $entity) {
            $entity->set($relation, null);
        }

        return $entities;
    }

    public function addEagerConstraints(array $entities): void
    {
        $this->eagerConstraints = array_unique(array_filter($this->getKeys($entities)));
    }

    public function getEagerResults(array $entities): array
    {
        if (empty($this->eagerConstraints)) {
            return [];
        }

        return $this->repository
            ->where($this->typeColumn, $this->typeValue)
            ->whereIn($this->idColumn, $this->eagerConstraints);
    }

    public function match(array $entities, array $results, string $relation): array
    {
        $dictionary = [];

        foreach ($results as $result) {
            $key = $result->get($this->idColumn);
            $dictionary[$key] = $result;
        }

        foreach ($entities as $entity) {
            $id = $entity->get($entity->getPrimaryKeyName());
            $entity->set($relation, $dictionary[$id] ?? null);
        }

        return $entities;
    }

    protected function getKeys(array $entities): array
    {
        return array_map(fn($entity) => $entity->get($entity->getPrimaryKeyName()), $entities);
    }
}
