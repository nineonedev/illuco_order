<?php

namespace Framework\Database\ORM\Repositories;

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

    public static function __callStatic($method, $arguments)
    {
        $builder = query(new static());

        if (method_exists($builder, $method)) {
            return $builder->$method(...$arguments);
        }

        throw new \BadMethodCallException("Method [$method] does not exist.");
    }

    /**
     * @return Entity|SoftDeletes
     */
    public static function resolveEntity(array $attributes = [])
    {
        $class = static::entityClass();
        return new $class($attributes);
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

        $this->observer->fire(RepositoryEvent::AFTER_UPDATE, $entity);
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

    public function setWith(array $relations): self
    {
        $this->with = $relations;
        return $this;
    }

    public function loadRelations(array $entities): array
    {
        if (empty($this->with) || empty($entities)) return $entities;

        $loader = $this->preventsLazyLoading
            ? new EagerLoader()
            : new LazyLoader();

        $validRelations = array_filter($this->with, fn ($rel) => Rel::getRelation($entities[0], $rel));

        if (!empty($validRelations)) {
            $loader->load($entities, $validRelations);
        }

        return $entities;
    }

    public function query(): EntityQueryBuilder
    {
        return $this->builder;
    }
}