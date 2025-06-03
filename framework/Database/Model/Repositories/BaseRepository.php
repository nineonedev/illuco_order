<?php

namespace Framework\Database\Model\Repositories;

use Framework\Database\Contracts\ConnectionInterface;
use Framework\Database\Contracts\RepositoryInterface;
use Framework\Database\Model\Entities\Entity;
use Framework\Database\Model\EagerLoader;
use Framework\Database\Model\Model;
use Framework\Database\Query\Builder;
use Framework\Support\CallbackRegistry;
use Framework\Support\Collection;
use Framework\Support\Str;

abstract class BaseRepository implements RepositoryInterface
{
    protected ConnectionInterface $connection; 
    protected CallbackRegistry $callbacks;
    protected Model $model;
    protected array $with = [];
    protected string $table;

    public function __construct(
        ConnectionInterface $connection,
        CallbackRegistry $callbacks,
        Model $model
    )
    {
        $this->connection = $connection;
        $this->callbacks = $callbacks;
        $this->model = $model;
    }

    public function getTable():string
    {
        return $this->table;
    }

    public function query(): Builder
    {
        return db($this->table);
    }

    public function save(Entity $entity): bool
    {
        $isNew = !$entity->hasPrimaryKey();
        $changes = $entity->getChanges();

        if ($isNew) {
            $this->callbacks->dispatch('beforeCreate', $entity);
            $result = $this->insert($entity);
            $this->callbacks->dispatch('afterCreate', $entity);
        } elseif ($entity->isDirty()) {
            $this->callbacks->dispatch('beforeUpdate', $entity);
            $result = $this->update($entity, $changes);
            $this->callbacks->dispatch('afterUpdate', $entity);
        } else {
            return true;
        }

        $entity->syncOriginal();
        return $result;
    }

    public function delete(Entity $entity): bool
    {
        $this->callbacks->dispatch('beforeDelete', $entity);
        $result = $this->deleteById($entity->get($entity->getPrimaryKeyName()));
        $this->callbacks->dispatch('afterDelete', $entity);
        return $result;
    }

    public function find($id): ?Entity
    {
        $data = $this->query()->where($this->getPrimaryKey(), $id)->first();
        return $data ? $this->toEntity((array) $data) : null;
    }

    public function findMany(array $ids): array
    {
        return $this->query()
            ->whereIn($this->getPrimaryKey(), $ids)
            ->get();
    }

    /**
     * @return Entity[]
     */
    public function whereIn(string $column, array $values): Builder
    {
        $rows = $this->query()->whereIn($column, $values)->get(); 
        $this->applyEagerLoad($rows); 

    }
    

    public function all(): array
    {
        $rows = $this->query()->get();
        return $this->applyEagerLoad($rows);
    }

    public function where(string $column, $value): Builder
    {
        $rows = $this->query()->where($column, $value)->get();
        return $this->applyEagerLoad($rows);
    }

    protected function applyEagerLoad(array $entities): array
    {
        if (empty($this->with)) {
            return $entities;
        }

        $loader = new EagerLoader();
        $loader->load($entities, $this->with);

        return $entities;
    }

    protected function insert(Entity $entity): bool
    {
        return $this->query()->insert($entity->getAttributes());
    }

    protected function update(Entity $entity, array $changes): bool
    {
        return $this->query()
            ->where($this->getPrimaryKey(), $entity->get($this->getPrimaryKey()))
            ->update($changes) > 0;
    }

    protected function deleteById($id): bool
    {
        return $this->query()
            ->where($this->getPrimaryKey(), $id)
            ->delete() > 0;
    }

    protected function toEntity(array $data): Entity
    {
        $class = $this->getEntityClass();
        return new $class($data);
    }

    abstract protected function getEntityClass(): string;

    protected function getPrimaryKey(): string
    {
        return (new ($this->getEntityClass()))->getPrimaryKeyName();
    }
}
