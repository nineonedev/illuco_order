<?php

namespace Framework\Database\Model\Repositories;

use Framework\Database\Contracts\ConnectionInterface;
use Framework\Database\Contracts\RepositoryInterface;
use Framework\Database\Model\Entities\Entity;
use Framework\Database\Query\Builder;
use Framework\Database\Query\Grammars\Grammar;

class NullRepository implements RepositoryInterface
{
    public function query(): Builder
    {
        return new Builder(
            app(ConnectionInterface::class),
            app(Grammar::class),
            'none'
        );
    }

    public function save(Entity $entity): bool { return false; }
    public function delete(Entity $entity): bool { return false; }

    public function find($id): ?Entity { return null; }
    public function findMany(array $ids): array { return []; }
    public function all(): array { return []; }
    public function where(string $column, $value): array { return []; }
    public function whereIn(string $column, array $values): array { return []; }

    public function getTable(): string { return ''; }
    public function with(array $relations) { return $this; }
}
