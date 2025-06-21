<?php

namespace Framework\Database\ORM\Repositories;

use Exception;
use Framework\Database\ORM\Entities\Entity;
use Framework\Database\ORM\Loaders\EagerLoader;
use Framework\Database\ORM\Loaders\LazyLoader;
use Framework\Database\ORM\Rel;
use Framework\Database\ORM\Traits\SoftDeletes;
use Framework\Database\Query\Builder;
use Framework\Database\Query\EntityQueryBuilder;
use RuntimeException;

abstract class Repository
{
    protected bool $withTrashed = false;
    protected bool $onlyTrashed = false;
    protected bool $preventsLazyLoading = true;

    protected Builder $builder;
    protected Observer $observer;

    /** @var string[] */
    protected array $with = [];

    protected array $withTree = []; 

    public function __construct()
    {
        $this->builder = query($this);
        $this->observer = new Observer();

        $this->initializeSoftDeleteFilter();
        $this->registerObservers();
    }

    protected function initializeSoftDeleteFilter(): void
    {
        if (!trait_used(SoftDeletes::class, static::entityClass())) return;

        $column = (static::resolveEntity())->getSoftDeleteColumn();

        if ($this->onlyTrashed) {
            $this->builder->whereNotNull($column);
        } elseif (!$this->withTrashed) {
            $this->builder->whereNull($column);
        }
    }

    /**
     * @return class-string<Entity>
     */
    abstract public static function entityClass(): string;

    abstract public static function table(): string;

    /**
     * @return static
     */
    public static function make()
    {
        return new static();
    }

    /**
     * @return Entity|SoftDeletes
     */
    public static function resolveEntity(array $attributes = [])
    {
        $class = static::entityClass();
        return new $class($attributes);
    }

    public function query(): EntityQueryBuilder
    {
        return $this->builder;
    }

    public static function queryStatic(): EntityQueryBuilder
    {
        return static::make()->query();
    }

    public function withTransaction(callable $callback)
    {
        return transaction()->run(fn () => $callback($this));
    }

    protected function registerObservers(): void {}

    public function on(string $event, string $observer): void
    {
        $this->observer->register($event, $observer);
    }

    public function save(Entity $entity): ?Entity
    {
        $this->observer->fire(RepositoryEvent::BEFORE_SAVE, $entity);

        return $entity->getPrimaryKey()
            ? $this->updateEntity($entity)
            : $this->insertEntity($entity);
    }

    protected function insertEntity(Entity $entity): ?Entity
    {
        $this->observer->fire(RepositoryEvent::BEFORE_CREATE, $entity);
        $id = $this->builder->insert($entity->getAttributes());

        if (!$id) return null;

        $entity->set($entity->getPrimaryKeyName(), $id);
        $this->observer->fire(RepositoryEvent::AFTER_CREATE, $entity);
        $this->observer->fire(RepositoryEvent::AFTER_SAVE, $entity);

        return $entity;
    }

    protected function updateEntity(Entity $entity): ?Entity
    {
        $this->observer->fire(RepositoryEvent::BEFORE_UPDATE, $entity);
        $affected = $this->builder
            ->where($entity->getPrimaryKeyName(), $entity->getPrimaryKey())
            ->update($entity->getAttributes());
            

        $this->observer->fire(RepositoryEvent::AFTER_SAVE, $entity);

        // return $affected > 0 ? $entity : null;
        return $entity;
    }

    public function delete(Entity $entity): bool
    {
        return $this->isSoftDeletable($entity)
            ? $this->performSoftDelete($entity)
            : $this->performHardDelete($entity);
    }

    protected function isSoftDeletable(Entity $entity): bool
    {
        return trait_used(SoftDeletes::class, $entity);
    }

    protected function performSoftDelete(Entity $entity): bool
    { 
        /** @var SoftDeletes|Entity $entity */
        $entity->markDeleted();
        
        $this->observer->fire(RepositoryEvent::BEFORE_DELETE, $entity);

        $column = $entity->getSoftDeleteColumn();

        $success = $this->builder
            ->where($entity->getPrimaryKeyName(), $entity->getPrimaryKey())
            ->update([$column => $entity->deletedAt()]);

        $this->observer->fire(RepositoryEvent::AFTER_DELETE, $entity);
        return (bool) $success;
    }

    protected function performHardDelete(Entity $entity): bool
    {
        $this->observer->fire(RepositoryEvent::BEFORE_DELETE, $entity);

        $success = $this->builder
            ->where('id', $entity->get('id'))
            ->delete() > 0;

        $this->observer->fire(RepositoryEvent::AFTER_DELETE, $entity);
        return $success;
    }


    public function withTrashed(): self
    {
        $this->withTrashed = true;
        $this->onlyTrashed = false;
        return $this;
    }

    public function onlyTrashed(): self
    {
        $this->onlyTrashed = true;
        $this->withTrashed = false;
        return $this;
    }

    public function withoutTrashed(): self
    {
        $this->onlyTrashed = false;
        $this->withTrashed = false;
        return $this;
    }

    public function with(array $relations): self
    {
        $relations = $this->flattenRelations($relations);

        $relations = array_map(function($rel) {
            if (is_string($rel) && class_exists($rel)) {
                return $rel::alias(); 
            }
            return $rel; 
        }, $relations);

        $this->with = $relations;
        $this->withTree = $this->mergeNestedRelations($relations);

        return $this;
    }
    protected function flattenRelations(array $relations, string $prefix = ''): array
    {
        $flattened = [];

        foreach ($relations as $key => $value) {
            if (is_array($value)) {
                $newPrefix = is_int($key) ? $prefix : trim("{$prefix}.{$key}", '.');
                $flattened = array_merge($flattened, $this->flattenRelations($value, $newPrefix));
            } else {
                $relation = is_int($key) ? $value : "{$key}.{$value}";
                $flattened[] = trim("{$prefix}.{$relation}", '.');
            }
        }

        return $flattened;
    }

    protected function mergeNestedRelations(array $relations): array
    {
        $tree = [];

        foreach ($relations as $relation) {
            $segments = explode('.', $relation);
            $ref = &$tree;

            foreach ($segments as $segment) {
                if (!isset($ref[$segment])) {
                    $ref[$segment] = [];
                }
                $ref = &$ref[$segment];
            }
        }

        return $tree;
    }

    public function loadRelations(array $entities): array
    {
        if (empty($this->with) || empty($entities)) return $entities;

        $loader = $this->preventsLazyLoading
            ? new EagerLoader()
            : new LazyLoader();

        // string 기반 중첩 경로 전달 (예: cart.items.product.values)
        $loader->load($entities, $this->with);

        return $entities;
    }


    public function find($id): ?Entity
    {
        return $this->query()->find($id);
    }

    public function findOrFail($id): Entity
    {
        $entity = $this->find($id);
        if (!$entity) {
            throw new RuntimeException('Entity not found.');
        }
        return $entity;
    }

    public function all(): array
    {
        return $this->query()->get();
    }

    public function findWhere(array $conditions): ?Entity
    {
        $query = $this->query();
        foreach ($conditions as $key => $value) {
            $query->where($key, $value);
        }
        return $query->first();
    }

    public function findWhereOrFail(array $conditions): Entity
    {
        $entity = $this->findWhere($conditions);
        if (!$entity) {
            throw new RuntimeException('Entity not found for given condition.');
        }
        return $entity;
    }

    public function getWhere(array $conditions): array
    {
        $query = $this->query();
        foreach ($conditions as $key => $value) {
            $query->where($key, $value);
        }
        return $query->get();
    }

}