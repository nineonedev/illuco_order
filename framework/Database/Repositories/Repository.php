<?php

namespace Framework\Database\Repositories;

use Framework\Database\Contracts\ConnectionInterface;
use Framework\Database\Contracts\RepositoryInterface;
use Framework\Database\Entities\Entity;
use Framework\Database\Paginator\Paginator;
use Framework\Database\Query\Builder;
use Framework\Support\Str;

abstract class Repository implements RepositoryInterface
{
    protected ConnectionInterface $connection;
    protected string $table;
    protected string $entityClass;
    protected string $primaryKey = 'id';
    protected array $with = [];

    public function __construct(ConnectionInterface $connection)
    {
        $this->connection = $connection;
        $this->boot();
    }

    protected function boot(): void
    {
        $this->table = $this->resolveTableName();
        $this->entityClass = $this->resolveEntityClass();
    }

    public function getBuilder(): Builder
    {
        return $this->connection->table($this->table);
    }

    public function getTable(): string
    {
        return $this->table;
    }

    public function createEntity(array $row): Entity
    {
        return $this->toEntity($row);
    }

    public function getEntityClass(): string
    {
        return $this->entityClass;
    }

    public function with(array $relations): self
    {
        $this->with = $relations;
        return $this;
    }

    public function find($id): ?Entity
    {
        $row = $this->getBuilder()->where($this->primaryKey, '=', $id)->first();
        return $row ? $this->toEntity((array) $row) : null;
    }

    public function findBy(string $column, $value): ?Entity
    {
        $row = $this->getBuilder()->where($column, '=', $value)->first();
        return $row ? $this->toEntity((array) $row) : null;
    }

    public function all(): array
    {
        $rows = $this->getBuilder()->get();
        $entities = array_map(fn($row) => $this->toEntity((array) $row), $rows);
        return $this->loadRelations($entities);
    }

    public function save(Entity $entity): bool
    {
        $data = $entity->getAttributes();

        if ($entity->isDirty()) {
            if (isset($data[$this->primaryKey])) {
                return $this->getBuilder()->where($this->primaryKey, '=', $data[$this->primaryKey])->update($data) > 0;
            }

            $ok = $this->getBuilder()->insert($data);
            if ($ok) {
                $entity->syncOriginal();
            }
            return $ok;
        }

        return true;
    }

    public function delete(Entity $entity): bool
    {
        $id = $entity->get($this->primaryKey);
        return $id ? $this->getBuilder()->where($this->primaryKey, '=', $id)->delete() > 0 : false;
    }

    public function paginate(int $perPage = 15, int $page = 1): Paginator
    {
        $paginator = $this->getBuilder()->paginate($perPage, $page);
        $items = array_map(fn($row) => $this->toEntity((array) $row), $paginator->items());

        return new Paginator($items, $paginator->total(), $perPage, $page);
    }

    protected function toEntity(array $row): Entity
    {
        return new $this->entityClass($row);
    }

    protected function loadRelations(array $entities): array
    {
        if (empty($this->with) || empty($entities)) return $entities;

        $sample = $entities[0];

        foreach ($this->with as $relationName) {
            $method = $relationName . 'Relation';

            if (!method_exists($sample, $method)) {
                continue;
            }

            $relation = $sample->{$method}();
            $map = $relation->getEagerResults($entities);

            foreach ($entities as $entity) {
                $foreignValue = $entity->{$relation->getLocalKey()};

                if (method_exists($relation, 'attach')) {
                    // many 관계
                    $entity->{$relationName} = $map[$foreignValue] ?? [];
                } else {
                    // one 관계
                    $entity->{$relationName} = $map[$foreignValue] ?? null;
                }
            }
        }

        return $entities;
    }


    protected function resolveTableName(): string
    {
        $name = Str::classBasename(static::class);
        return Str::snake(Str::plural(Str::before($name, 'Repository')));
    }

    protected function resolveEntityClass(): string
    {
        $repo = static::class;
        $base = Str::beforeLast($repo, '\\Repositories');
        $name = Str::before(Str::classBasename($repo), 'Repository');
        return "{$base}\\Entities\\{$name}";
    }
}
