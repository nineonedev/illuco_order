<?php

namespace Framework\Database\Contracts;

use Framework\Database\Entities\Entity;
use Framework\Database\Query\Builder;
use Framework\Database\Paginator\Paginator;

interface RepositoryInterface
{
    public function find($id): ?Entity;

    public function findBy(string $column, $value): ?Entity;

    public function all(): array;

    public function getPrimaryKeyName(): string;
    
    public function with(array $relations): RepositoryInterface;

    public function save(Entity $entity): bool;

    public function delete(Entity $entity): bool;

    public function getBuilder(): Builder;

    public function getTable(): string;

    public function getEntityClass(): string;


    public function paginate(int $perPage = 15, int $page = 1): Paginator;

    public function createEntity(array $row): Entity;

    public function hasPrimarykey(Entity $entity): bool;
}
