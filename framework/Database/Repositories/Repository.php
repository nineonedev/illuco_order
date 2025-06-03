<?php

namespace Framework\Database\Repositories;

use Framework\Database\Contracts\ConnectionInterface;
use Framework\Database\Contracts\RepositoryInterface;
use Framework\Database\Entities\Entity;
use Framework\Database\Paginator\Paginator;
use Framework\Database\Query\Builder;
use Framework\Database\Relations\EagerLoader;
use Framework\Support\CallbackRegistry;
use Framework\Support\Str;

abstract class Repository implements RepositoryInterface
{
    public const BEFORE_CREATE = 'beforeCreate'; 
    public const AFTER_CREATE = 'afterCreate'; 
    public const BEFORE_UPDATE = 'beforeUpdate'; 
    public const AFTER_UPDATE = 'afterUpdate'; 
    public const BEFORE_DELETE = 'beforeDelete'; 
    public const AFTER_DELETE = 'afterDelete';
    public const BEFORE_SAVE = 'beforeSave'; 
    public const AFTER_SAVE = 'afterSave';

    protected CallbackRegistry $callbackRegistry;
    protected ConnectionInterface $connection;

    protected string $table;
    protected string $entityClass;
    protected string $primaryKey = 'id';
    protected array $with = [];

    protected array $observers = [];

    protected array $eagerLoad = [];


    public function __construct(
        ConnectionInterface $connection,
        CallbackRegistry $callbackRegistry
    ) {
        $this->connection = $connection;
        $this->callbackRegistry = $callbackRegistry;

        $this->boot();
        $this->registerObservers();
    }

    

    public function getPrimaryKeyName(): string
    {
        return $this->primaryKey;
    }

    protected function boot(): void
    {
        $this->table = $this->resolveTableName();
        $this->entityClass = $this->resolveEntityClass();
    }

    protected function registerObservers(): void
    {
        foreach ($this->observers as $hook => $observerClasses) {
            foreach ((array) $observerClasses as $observerClass) {
                $observer = app($observerClass);

                $this->callbackRegistry->register(
                    $this->entityClass, 
                    $hook,
                    $observer
                );
            }
        }
    }

    protected function fireObservers(string $hook, Entity $entity): void
    {
        $this->callbackRegistry->dispatch($hook, $entity);
    }

    public function getBuilder(): Builder
    {
        return $this->connection->table($this->table);
    }

    public function getTable(): string
    {
        return $this->table;
    }

    public function getEntityClass(): string
    {
        return $this->entityClass;
    }

    public function createEntity(array $row): Entity
    {
        return $this->toEntity($row);
    }

    protected function toEntity(array $row): Entity
    {
        return new $this->entityClass($row);
    }

    public function hasPrimaryKey(Entity $entity): bool
    {
        $key = $this->primaryKey;
        return !empty($entity->get($key));
    }

    public function with(array $relations): RepositoryInterface
    {
        $this->eagerLoad = $this->parseWith($relations);
        return $this;
    }


    protected function parseWith(array $relations): array
    {
        $result = [];

        foreach ($relations as $relation) {
            if (is_string($relation)) {
                $segments = explode('.', $relation);
                $this->buildRelationTree($result, $segments);
            }
        }

        return $result;
    }

    protected function buildRelationTree(array &$tree, array $segments): void
    {
        $segment = array_shift($segments);
        if (!isset($tree[$segment])) {
            $tree[$segment] = [];
        }

        if (!empty($segments)) {
            $this->buildRelationTree($tree[$segment], $segments);
        }
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
        $records = $this->getBuilder()->get();

        $entities = array_map(
            fn($row) => $this->createEntity((array)$row),
            $records
        );

        if (!empty($this->eagerLoad)) {
            (new EagerLoader())->load($entities, $this->eagerLoad);
        }

        return $entities;
    }

    public function save(Entity $entity): bool
    {
        if (! $entity->isDirty()) {
            return true;
        }

        $this->fireObservers(self::BEFORE_SAVE, $entity);

        $ok = null;
        if (!$this->hasPrimaryKey($entity)) {
            $ok = $this->create($entity);
        } else {
            $ok = $this->update($entity);
        }

        $this->fireObservers(self::AFTER_SAVE, $entity);

        return $ok ?? false;
    }


    public function create(Entity $entity): bool
    {
        if ($this->hasPrimaryKey($entity)) {
            return false; 
        }

        $this->fireObservers(self::BEFORE_CREATE, $entity);

        $ok = $this->getBuilder()->insert($entity->getAttributes());

        if ($ok) {
            $entity->syncOriginal();

            $this->fireObservers(self::AFTER_CREATE, $entity);
        }

        return $ok;
    }

    public function update(Entity $entity): bool
    {
        if (!$this->hasPrimaryKey($entity)) {
            return false; 
        }

        $this->fireObservers(self::BEFORE_UPDATE, $entity);

        $ok = $this->getBuilder()
            ->where($this->primaryKey, '=', $entity->get($this->primaryKey))
            ->update($entity->getAttributes());

        if ($ok) {
            $entity->syncOriginal();

            $this->fireObservers(self::AFTER_UPDATE, $entity);
        }

        return $ok;
    }

    public function delete(Entity $entity): bool
    {
        if (!$this->hasPrimaryKey($entity)) {
            return false; 
        }

        $this->fireObservers(self::BEFORE_DELETE, $entity);

        $ok = $this->getBuilder()
            ->where($this->primaryKey, '=', $entity->get($this->primaryKey))
            ->delete() > 0;

        if ($ok) {
            $this->fireObservers(self::AFTER_DELETE, $entity);
        }

        return $ok;
    }

    public function paginate(int $perPage = 15, int $page = 1): Paginator
    {
        $paginator = $this->getBuilder()->paginate($perPage, $page);
        $items = array_map(fn ($row) => $this->toEntity((array) $row), $paginator->items());

        return new Paginator($items, $paginator->total(), $perPage, $page);
    }

    protected function loadRelations(array $entities): array
    {
        if (empty($this->with) || empty($entities)) {
            return $entities;
        }

        $sample = $entities[0];

        foreach ($this->with as $relationName) {
            $method = $relationName;

            if (!method_exists($sample, $method)) {
                continue;
            }

            $relation = $sample->{$method}();
            $map = $relation->getEagerResults($entities);

            foreach ($entities as $entity) {
                $foreignValue = $entity->{$relation->getLocalKey()};

                if (method_exists($relation, 'attach')) {
                    $entity->{$relationName} = $map[$foreignValue] ?? [];
                } else {
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
