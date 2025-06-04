<?php

namespace Framework\Database\Model\Repositories;

use Framework\Database\Contracts\RepositoryInterface;
use Framework\Database\Model\Model;
use Framework\Database\Model\Entities\Entity;
use Framework\Database\Query\Builder;

abstract class Repository implements RepositoryInterface
{
    protected Model $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function getTable(): string
    {
        return $this->model->getTable();
    }

    public function getPrimaryKey(): string
    {
        return $this->model->getPrimaryKey();
    }

    public function query(): Builder
    {
        return db($this->getTable());
    }

    public function collect(array $rows)
    {
        return array_map(fn($row) => $this->makeEntity($row), $rows);
    }

    public function find($id): ?Entity
    {
        $row = $this->query()
            ->where($this->getPrimaryKey(), '=', $id)
            ->first();

        return $row ? $this->makeEntity((array) $row) : null;
    }

    public function all(): array
    {
        $rows = $this->query()->get();

        return $this->collect($rows);
    }

    public function where(string $column, $value): array
    {
        $rows = $this->query()->where($column, '=', $value)->get();

        return $this->collect($rows);
    }

    public function whereIn(string $column, array $values): array
    {
        $rows = $this->query()->whereIn($column, $values)->get();

        return $this->collect($rows);
    }

    public function save(Entity $entity): bool
    {
        $pk = $this->getPrimaryKey();
        $data = $entity->getAttributes();

        if ($entity->hasPrimaryKey()) {
            return $this->query()
                ->where($pk, '=', $entity->get($pk))
                ->update($data);
        }

        $id = $this->query()->insertGetId($data);
        $entity->set($pk, $id);

        return true;
    }

    public function delete(Entity $entity): bool
    {
        if ($entity->usesSoftDeletes()) {
            $entity->markAsDeleted();
            return $this->save($entity);
        }

        return $this->query()
            ->where($this->getPrimaryKey(), '=', $entity->get($this->getPrimaryKey()))
            ->delete();
    }

    protected function makeEntity(array $data): Entity
    {
        return $this->model->makeEntity($data);
    }
}
