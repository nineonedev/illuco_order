<?php

namespace Framework\Database\Query;

use Framework\Database\ORM\Entities\Entity;
use Framework\Database\ORM\Repositories\Repository;
use Framework\Database\Paginator\Paginator;
use Framework\Database\Entities\EntityCollection;

class EntityQueryBuilder extends Builder
{
    protected Repository $repository;

    public function __construct(
        $connection, 
        $grammar, 
        string $table, 
        Repository $repository
    )
    {
        parent::__construct($connection, $grammar, $table);
        $this->repository = $repository;
    }
    
    public function with(array $relations): self
    {
        $this->repository->setWith($relations);
        return $this;
    }

    public function get(): array
    {
        $rows = parent::get();
        $entities = array_map(fn($row) => $this->repository->createEntity((array)$row), $rows);
        return $this->repository->loadRelations($entities);
    }

    public function all(): array
    {
        return $this->get();
    }

    public function pluck(string $column): array
    {
        return array_map(
            fn($entity) => $entity->get($column),
            $this->get()
        );
    }

    public function toArray(): array
    {
        return array_map(fn($entity) => $entity->toArray(), $this->get());
    }

    public function toJson(): string
    {
        return json_encode($this->toArray(), JSON_UNESCAPED_UNICODE);
    }
}
