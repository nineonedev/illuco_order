<?php

namespace Framework\Database\Model;

use Framework\Database\Contracts\RepositoryInterface;
use Framework\Database\Model\Entities\Entity;
use Framework\Database\Query\Builder;
use Framework\Support\CallbackRegistry;

abstract class Repository implements RepositoryInterface
{
    protected string $table;
    protected string $entityClass;

    protected CallbackRegistry $callbacks;
    protected Entity $entity;

    protected array $with = [];
    protected bool $preventsLazyLoading = false;

    // Soft delete 제어
    protected bool $withTrashed = false;
    protected bool $onlyTrashed = false;

    public function __construct(array $data = [])
    {
        $this->ensureRequirements();
        $this->callbacks = new CallbackRegistry();
        $this->entity = $this->toEntity($data);
    }

    public function getTable(): string
    {
        return $this->table;
    }

    protected function ensureRequirements(): void
    {
        if (!$this->table) {
            throw new \Exception("table must be initialized.");
        }

        if (!$this->entityClass) {
            throw new \Exception("entityClass must be initialized.");
        }
    }

    public function toEntity(array $row = []): Entity
    {
        return new $this->entityClass($row); 
    }

    /**
     * @return Entity[]
     */
    public function toEntities(array $rows): array
    {   
        return array_map(fn($row) => $this->toEntity($row), $rows);;
    }

    public function with(array $relations): self
    {
        if (!$this->preventsLazyLoading) {
            return $this;
        }

        $this->with = $relations;
        return $this;
    }

    public function loadMissing(array $entities, array $relations): void
    {
        if (empty($entities) || empty($relations)) {
            return;
        }

        $first = $entities[0];

        $unloaded = array_filter($relations, function ($relation) use ($first) {
            return !$first->relationLoaded($relation);
        });

        if (!empty($unloaded)) {
            $this->silentlyLoad($entities, $unloaded);
        }
    }

    protected function eagerLoadIfNecessary(array $entities): void
    {
        if ($this->preventsLazyLoading && !empty($this->with)) {
            $this->silentlyLoad($entities, $this->with);
            $this->preventsLazyLoading = false;
        }
    }

    public function silentlyLoad(array $entities, array $relations): void
    {
        $loader = new EagerLoader();
        $loader->load($entities, $relations);
    }

    // =========================================================
    // CRUD operations
    // =========================================================

    public function query(): Builder
    {
        $query = db($this->table);

        if ($this->entity->usesSoftDeletes()) {
            $column = $this->entity->softDeleteColumn();

            if ($this->onlyTrashed) {
                $query->whereNotNull($column);
            } elseif (!$this->withTrashed) {
                $query->whereNull($column);
            }
        }

        return $query;
    }

    public static function all(): array
    {
        $repo = new static();

        $rows = $repo->query()->get();

        $entities = $repo->toEntities($rows);

        $repo->eagerLoadIfNecessary($entities);

        return $entities;
    }

    public static function find($id): ?Entity
    {
        $repo = new static();

        $row = $repo->query()
            ->where($repo->entity->getPrimaryKeyName(), $id)
            ->first();

        if (!$row) {
            return null;
        }

        $entity = $repo->toEntity((array) $row);

        $repo->eagerLoadIfNecessary([$entity]);

        return $entity;
    }

    public static function create(array $attributes = []): bool
    {
        $repo = new static();
        $data = $repo->toEntity($attributes);

        $repo->callbacks->dispatch('creating', $data);
        $result = $repo->query()->insert($data->getAttributes());
        $repo->callbacks->dispatch('created', $data);

        return $result; 
    }

    public function save(): bool
    {
        $isNew = $this->entity->hasPrimaryKey();
        $result = null;

        if ($isNew) {
            $result = static::create($this->entity->getAttributes());
            $this->entity->set($this->entity->getPrimaryKeyName(), $this->query()->lastInsertId());

        } else {
            if ($this->entity->isClean()) {
                return true;
            }

            $changes = $this->entity->getChanges();
            
            $this->callbacks->dispatch('updating', $this->entity);
            $result = $this->query()
                ->where($this->entity->getPrimaryKeyName(), $this->entity->getPrimaryKey())
                ->update($changes);
            $this->callbacks->dispatch('updated', $this->entity);
        }

        return (bool) $result;
    }

    public function delete(): bool
    {
        if ($this->entity->usesSoftDeletes()) {
            $this->entity->markDeleted();
            return $this->save($this->entity);
        }

        return $this->forceDelete($this->entity);
    }

    // Soft delete query modifiers

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
        $this->withTrashed = false;
        $this->onlyTrashed = false;
        return $this;
    }

    public function restore(Entity $entity): bool
    {
        if (!$entity->usesSoftDeletes()) {
            throw new \LogicException('Entity does not support soft deletes.');
        }

        if (! $entity->isDeleted()) {
            return true;
        }

        $entity->restore();
        return $this->save($entity);
    }

    public function forceDelete(Entity $entity): bool
    {
        $this->callbacks->dispatch('deleting', $entity);

        $result = $this->query()
            ->where($entity->getPrimaryKeyName(), $entity->getPrimaryKey())
            ->delete();

        $this->callbacks->dispatch('deleted', $entity);

        return (bool) $result;
    }

}
