<?php

namespace Framework\Database\Model\Relations;

use Framework\Database\Model\Entities\Entity;
use Framework\Database\Contracts\RepositoryInterface;

class HasMany extends Relation
{
    protected string $foreignKey;
    protected string $localKey;
    protected array $eagerConstraints = [];

    public function __construct(
        Entity $parent,
        RepositoryInterface $repository,
        string $foreignKey,
        string $localKey = 'id'
    ) {
        parent::__construct($parent, $repository);
        $this->foreignKey = $foreignKey;
        $this->localKey = $localKey;
    }

    public function getResults(): array
    {
        $value = $this->parent->get($this->localKey);

        if (!$value) {
            return [];
        }

        return $this->repository->where($this->foreignKey, $value);
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
        $this->eagerConstraints = array_unique(array_filter($this->getKeys($entities)));
    }

    public function getEagerResults(array $entities): array
    {
        if (empty($this->eagerConstraints)) {
            return [];
        }

        return $this->repository->whereIn($this->foreignKey, $this->eagerConstraints);
    }

    public function match(array $entities, array $results, string $relation): array
    {
        $dictionary = [];

        foreach ($results as $result) {
            $key = $result->get($this->foreignKey);
            $dictionary[$key][] = $result;
        }

        foreach ($entities as $entity) {
            $value = $entity->get($this->localKey);
            $entity->set($relation, $dictionary[$value] ?? []);
        }

        return $entities;
    }

    protected function getKeys(array $entities): array
    {
        return array_map(fn($entity) => $entity->get($this->localKey), $entities);
    }
}
