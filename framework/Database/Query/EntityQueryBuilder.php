<?php

namespace Framework\Database\Query;

use Framework\Database\ORM\Repositories\Repository;

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

    public function find($id)
    {
        $pk = $this->repository->createEntity([])->getPrimaryKeyName();

        // 여러 개
        if (is_array($id)) {
            $entities = $this->whereIn($pk, $id)->get();
            return $entities;
        }

        // 단일
        $entity = $this->where($pk, $id)->first();
        if ($entity) {
            // with() 사용시 관계도 포함
            $entities = $this->repository->loadRelations([$entity]);
            return $entities[0] ?? $entity;
        }

        return null;
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
