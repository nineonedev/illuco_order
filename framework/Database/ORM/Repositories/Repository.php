<?php

namespace Framework\Database\ORM\Repositories;

use Framework\Database\ORM\Entities\Entity;
use Framework\Database\ORM\Entities\EntityCollection;
use Framework\Database\ORM\Loaders\EagerLoader;
use Framework\Database\ORM\Loaders\LazyLoader;
use Framework\Database\ORM\Traits\SoftDeletes;
use Framework\Database\ORM\ORM;
use Framework\Database\Query\Builder;
use Framework\Database\Query\EntityQueryBuilder;

/**
 * @method static EntityQueryBuilder with(array $relations)
 * @method static array toArray()
 * @method static string toJson()
 */

abstract class Repository
{
    protected string $table;

    protected bool $withTrashed = false;
    
    protected bool $onlyTrashed = false;

    protected bool $preventsLazyLoading = false; 

    protected Entity $entity;

    protected Builder $builder;

    protected Observer $observer; 

    /**
     * @var array<string,array<class-string<Obserable>|Obserable>>
     */
    protected array $obserables = [];

    /** @var string[] */
    protected array $with = [];

    public function __construct(array $attributes = [])
    {
        $this->builder = query($this);
        $this->observer = new Observer();
        $this->entity = $this->createEntity($attributes);
        $this->setup();
    }

    abstract public function table(): string;

    abstract public function entityClass(): string; 
    

    protected function setup()
    {
        foreach ($this->obserables as $event => $obs) {
            $this->observer->registerMany($event, $obs);
        }

        $this->applySoftDeleteFilter();
    }

    public function save(): bool
    {
        $entity = $this->entity;

        $this->observer->fire(RepositoryEvent::BEFORE_SAVE, $entity);

        $data = $entity->toArray();
        $pkName = $entity->getPrimaryKeyName();
        $isNew = empty($entity->getPrimaryKey());

        $success = false;

        if ($isNew) {
            
            $this->observer->fire(RepositoryEvent::BEFORE_CREATE, $entity);

            $id = $this->builder->insertGetId($data);

            if ($id) {

                $entity->set($pkName, $id);

                $this->observer->fire(RepositoryEvent::AFTER_CREATE, $entity);

                $success = true;
            }
        } else {
            $this->observer->fire(RepositoryEvent::BEFORE_UPDATE, $entity);

            $success = $this->builder->where($pkName, $entity->getPrimaryKey())->update($data) > 0;

            $this->observer->fire(RepositoryEvent::AFTER_UPDATE, $entity);
        }

        $this->observer->fire(RepositoryEvent::AFTER_SAVE, $entity);

        return $success;
    }

    public function delete(): bool
    {
        $entity = $this->entity;
        
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
            $relation = ORM::getRelation($entities[0], $relationName);
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
