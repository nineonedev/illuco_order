<?php

namespace Framework\Database\Repositories;

use Framework\Database\Contracts\RepositoryInterface;
use Framework\Database\Entities\Entity;
use Framework\Database\Query\Builder;
use Framework\Database\Paginator\Paginator;

class NullRepository implements RepositoryInterface
{
    protected function fail(string $method): void
    {
        throw new \LogicException("Method [{$method}] is not supported in NullRepository.");
    }

    public function find($id): ?Entity
    {
        $this->fail(__FUNCTION__);
    }

    public function findBy(string $column, $value): ?Entity
    {
        $this->fail(__FUNCTION__);
    }

    public function all(): array
    {
        $this->fail(__FUNCTION__);
    }

    public function save(Entity $entity): bool
    {
        $this->fail(__FUNCTION__);
    }

    public function delete(Entity $entity): bool
    {
        $this->fail(__FUNCTION__);
    }

    public function getBuilder(): Builder
    {
        $this->fail(__FUNCTION__);
    }

    public function getTable(): string
    {
        $this->fail(__FUNCTION__);
    }

    public function getEntityClass(): string
    {
        $this->fail(__FUNCTION__);
    }

    public function with(array $relations): RepositoryInterface
    {
        $this->fail(__FUNCTION__);
    }

    public function paginate(int $perPage = 15, int $page = 1): Paginator
    {
        $this->fail(__FUNCTION__);
    }

    public function createEntity(array $row): Entity
    {
        $this->fail(__FUNCTION__);
    }

    public function hasPrimarykey(Entity $entity): bool
    {
        $this->fail(__FUNCTION__);
    }
}
