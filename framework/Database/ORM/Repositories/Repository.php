<?php

namespace Framework\Database\ORM\Repositories;

use Framework\Database\ORM\Entities\Entity;
use Framework\Database\ORM\Loaders\EagerLoader;
use Framework\Database\ORM\Loaders\LazyLoader;
use Framework\Database\ORM\RelationMap;
use Framework\Database\ORM\Traits\SoftDeletes;
use Framework\Database\Query\Builder;
use Framework\Database\Query\EntityQueryBuilder;

/**
 * @method static EntityQueryBuilder with(array $relations)
 * @method static array toArray()
 * @method static string toJson()
 */

abstract class Repository
{
    static ?Repository $instance = null;
    
    protected string $table;

    protected bool $withTrashed = false;
    
    protected bool $onlyTrashed = false;

    protected bool $preventsLazyLoading = false; 

    protected Builder $builder;

    protected Observer $observer; 

    /** @var string[] */
    protected array $with = [];

    public function __construct()
    {
        $this->builder = query($this);
        $this->observer = new Observer();
        $this->setup();
    }

    /**
     * @return static
     */
    public static function new()
    {
        if (!static::$instance) {
            static::$instance = new static();
        }
        
        return static::$instance; 
    }

    abstract public function table(): string;

    abstract public function entityClass(): string; 
    
    /**
     * 하위 레포지토리에서 이벤트/옵저버 등록처
     */
    protected function registerObservers(): void
    {
    }

    protected function setup()
    {
        $this->registerObservers();
        $this->applySoftDeleteFilter();
    }

    /**
     * @param class-string<Observeralbe> $observer
     */
    public function on(string $event, string $observer): void
    {
        $this->observer->register($event, $observer);
    }

    public function save(Entity $entity): ?Entity
    {
        $this->observer->fire(RepositoryEvent::BEFORE_SAVE, $entity);

        $pkName = $entity->getPrimaryKeyName();
        $isNew = is_null($entity->getPrimaryKey());

        if ($isNew) {
            $this->observer->fire(RepositoryEvent::BEFORE_CREATE, $entity);
            $id = $this->builder->insert($entity->getAttributes());

            if (!$id) {
                return null;
            } 
            
            $entity->set($pkName, $id);
            $this->observer->fire(RepositoryEvent::AFTER_CREATE, $entity);
            $this->observer->fire(RepositoryEvent::AFTER_SAVE, $entity);
            return $entity;
        }
        
        $this->observer->fire(RepositoryEvent::BEFORE_UPDATE, $entity);
        $affected = $this->builder->where($pkName, $entity->getPrimaryKey())->update($entity->getAttributes());
        $this->observer->fire(RepositoryEvent::AFTER_UPDATE, $entity);
        $this->observer->fire(RepositoryEvent::AFTER_SAVE, $entity);

        return $affected > 0 ? $entity : null;
    }

    public function delete(Entity $entity): bool
    {
        if (trait_used(SoftDeletes::class, $entity)) {
            return $this->softDelete($entity); 
        }

        $this->observer->fire(RepositoryEvent::BEFORE_DELETE, $entity);
        $success = $this->builder->where('id', $entity->get('id'))->delete() > 0;
        $this->observer->fire(RepositoryEvent::AFTER_DELETE, $entity);
        return $success;
    }

    protected function applySoftDeleteFilter(): void
    {
        $entityClass = $this->entityClass();
        if (trait_used(SoftDeletes::class, $entityClass)) {
            
            /** @var SoftDeletes $entity */
            $entity = new $entityClass();
            $column = $entity->getSoftDeleteColumn();

            if ($this->onlyTrashed) {
                $this->builder->whereNotNull($column);
            } elseif (!$this->withTrashed) {
                $this->builder->whereNull($column);
            }
        }
    }
    
    protected function softDelete(Entity $entity)
    {
        /** @var SoftDeletes $entity */
        $entity->markDeleted();

        // 바로 DB에 반영
        $this->observer->fire(RepositoryEvent::BEFORE_DELETE, $entity);
        
        $pkName = $entity->getPrimaryKeyName();

        $success = $this->builder
            ->where($pkName, $entity->getPrimaryKey())
            ->update([$entity->getSoftDeleteColumn() => $entity->deletedAt()]);

        $this->observer->fire(RepositoryEvent::AFTER_DELETE, $entity);
        return (bool) $success;
    }

    /*** --- 관계 with/로드 --- ***/
    public function setWith(array $relations): self
    {
        $this->with = $relations;
        return $this;
    }

    /**
     * @param Entity[] $entities
     * @return Entity[]
     */
    public function loadRelations(array $entities): array
    {
        if (empty($this->with) || empty($entities)) return $entities;

        $loader = $this->preventsLazyLoading
            ? new EagerLoader()
            : new LazyLoader();

        $relations = [];

        foreach ($this->with as $relationName) {
            $relation = RelationMap::getRelation($entities[0], $relationName);
            if (!$relation) continue;
            $relations[] = $relationName;
        }

        if (!empty($relations)) {
            $loader->load($entities, $relations);
        }

        return $entities;
    }

    /*** --- Entity 변환 --- ***/
    public function createEntity(array $attributes): Entity
    {
        $class = $this->entityClass();
        return new $class($attributes);
    }

    // 빌더 체이닝용
    public function query(): EntityQueryBuilder
    {
        return $this->builder;
    }

    /*** --- SoftDeletes --- ***/
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

    public static function __callStatic($method, $arguments)
    {
        $builder = query(static::class);

        if (method_exists($builder, $method)) {
            return $builder->$method(...$arguments);
        }

        throw new \BadMethodCallException("Method [$method] does not exist.");
    }
}
