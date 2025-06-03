<?php

namespace Framework\Database\Model\Relations;

use Framework\Database\Model\Entities\Entity;
use Framework\Database\Contracts\RepositoryInterface;

class BelongsTo extends Relation
{
    protected string $foreignKey;
    protected string $ownerKey;
    protected array $eagerConstraints = [];

    public function __construct(
        Entity $parent,
        RepositoryInterface $repository,
        string $foreignKey,
        string $ownerKey = 'id'
    ) {
        parent::__construct($parent, $repository);
        $this->foreignKey = $foreignKey;
        $this->ownerKey = $ownerKey;
    }

    public function getResults(): ?Entity
    {
        $foreignValue = $this->parent->get($this->foreignKey);

        if (!$foreignValue) {
            return null;
        }

        $results = $this->repository->where($this->ownerKey, $foreignValue);

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

        return $this->repository->whereIn($this->ownerKey, $this->eagerConstraints);
    }

    public function match(array $entities, array $results, string $relation): array
    {
        $dictionary = [];

        foreach ($results as $result) {
            $key = $result->get($this->ownerKey);
            $dictionary[$key] = $result;
        }

        foreach ($entities as $entity) {
            $foreignValue = $entity->get($this->foreignKey);
            $entity->set($relation, $dictionary[$foreignValue] ?? null);
        }

        return $entities;
    }

    protected function getKeys(array $entities): array
    {
        return array_map(fn($entity) => $entity->get($this->foreignKey), $entities);
    }
}
