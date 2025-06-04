<?php

namespace Framework\Database\Contracts;

use Framework\Database\Model\Entities\Entity;
use Framework\Database\Query\Builder;

interface RepositoryInterface
{
    /**
     * @return static
     */
    public function with(array $relations);

    public function query(): Builder;
    
    public function getTable(): string;

    public function save(Entity $entity): bool;

    public function delete(Entity $entity): bool;

    public function find($id): ?Entity;

    public function findMany(array $ids): array;

    public function all(): array;

    public function where(string $column, $value): array;

    public function whereIn(string $column, array $values): array;
}
