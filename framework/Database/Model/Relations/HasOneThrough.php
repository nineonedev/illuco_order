<?php

namespace Framework\Database\Model\Relations;

use Framework\Database\Model\Entities\Entity;
use Framework\Database\Contracts\RepositoryInterface;

class HasOneThrough extends Relation
{
    protected string $throughTable;
    protected string $throughKey;
    protected string $firstKey;
    protected string $secondKey;
    protected string $localKey;
    protected array $eagerParentIds = [];

    public function __construct(
        Entity $parent,
        RepositoryInterface $repository,
        string $throughTable,
        string $firstKey,
        string $secondKey,
        string $throughKey = 'id',
        string $localKey = 'id'
    ) {
        parent::__construct($parent, $repository);

        $this->throughTable = $throughTable;
        $this->throughKey = $throughKey;
        $this->firstKey = $firstKey;
        $this->secondKey = $secondKey;
        $this->localKey = $localKey;
    }

    public function getResults(): ?Entity
    {
        $parentId = $this->parent->get($this->localKey);

        if (!$parentId) {
            return null;
        }

        $results = $this->repository
            ->query()
            ->join($this->throughTable, $this->throughTable.'.'.$this->firstKey, '=', $this->repository->getTable().'.'.$this->secondKey)
            ->where($this->throughTable.'.'.$this->throughKey, $parentId)
            ->get();

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
        $this->eagerParentIds = array_unique(array_filter($this->getKeys($entities)));
    }

    public function getEagerResults(array $entities): array
    {
        if (empty($this->eagerParentIds)) {
            return [];
        }

        return $this->repository
            ->query()
            ->join($this->throughTable, $this->throughTable.'.'.$this->firstKey, '=', $this->repository->getTable().'.'.$this->secondKey)
            ->whereIn($this->throughTable.'.'.$this->throughKey, $this->eagerParentIds)
            ->get();
    }

    public function match(array $entities, array $results, string $relation): array
    {
        $dictionary = [];

        foreach ($results as $result) {
            $key = $result->get($this->throughTable.'.'.$this->throughKey);
            if (!isset($dictionary[$key])) {
                $dictionary[$key] = $result;
            }
        }

        foreach ($entities as $entity) {
            $key = $entity->get($this->localKey);
            $entity->set($relation, $dictionary[$key] ?? null);
        }

        return $entities;
    }

    protected function getKeys(array $entities): array
    {
        return array_map(fn($entity) => $entity->get($this->localKey), $entities);
    }
}
