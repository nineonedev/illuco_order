<?php

namespace Framework\Database\Model\Relations;

use Framework\Database\Model\Entities\Entity;
use Framework\Database\Contracts\RepositoryInterface;

abstract class Relation
{
    protected Entity $parent;
    protected RepositoryInterface $repository;

    public function __construct(Entity $parent, RepositoryInterface $repository)
    {
        $this->parent = $parent;
        $this->repository = $repository;
    }

    abstract public function getResults();

    abstract public function getEagerResults(array $entities): array;

    abstract public function initRelation(array $entities, string $relation): array;

    abstract public function match(array $entities, array $results, string $relation): array;

    abstract public function addEagerConstraints(array $entities): void;

    abstract protected function getKeys(array $entities): array;

    public function getParent(): Entity
    {
        return $this->parent;
    }

    public function getRepository(): RepositoryInterface
    {
        return $this->repository;
    }
}
