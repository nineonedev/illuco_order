<?php

namespace Framework\Database\Query;

use Framework\Database\ORM\Entities\Entity;
use Framework\Database\ORM\Relations\Pivot;
use Framework\Database\ORM\Repositories\Repository;

class EntityQueryBuilder extends Builder
{
    protected Repository $repository;

    public function __construct(
        $connection, 
        $grammar, 
        string $table, 
        Repository $repository
    )
    {
        parent::__construct($connection, $grammar, $table);
        $this->repository = $repository;
    }
    
    public function with(array $relations): self
    {
        $this->repository->with($relations);
        return $this;
    }

    public function get(): array
    {
        $rows = parent::get();

        $entities = array_map(function ($row) {
            $attributes = (array) $row;

            $pivotAttributes = [];
            foreach ($attributes as $key => $value) {
                if (strpos($key, 'pivot_') === 0) {
                    $pivotKey = substr($key, 6); // pivot_created_at → created_at
                    $pivotAttributes[$pivotKey] = $value;
                    unset($attributes[$key]);
                }
            }

            /** @var Entity $entity */ 
            $entity = get_class($this->repository)::resolveEntity($attributes);

            // Pivot 분리하여 relation에 할당
            if (!empty($pivotAttributes)) {
                $pivot = new Pivot($pivotAttributes);
                $pivot->setPivotParent($entity);
                $entity->setRelation('pivot', $pivot);
            }

            return $entity;
        }, $rows);

        return $this->repository->loadRelations($entities);
    }


    public function all(): array
    {
        return $this->get();
    }

    public function pluck(string $column, ?string $key = null): array
    {
        $entities = $this->get();

        if ($key === null) {
            return array_map(
                fn($entity) => $entity->get($column),
                $entities
            );
        }

        $result = [];
        foreach ($entities as $entity) {
            $result[$entity->get($key)] = $entity->get($column);
        }

        return $result;
    }


    public function find($id)
    {
        $pk = get_class($this->repository)::resolveEntity([])->getPrimaryKeyName();

        // 여러 개
        if (is_array($id)) {
            $entities = $this->whereIn($pk, $id)->get();
            return $entities;
        }

        // 단일
        $entity = $this->where($pk, $id)->first();
        if ($entity) {
            // with() 사용시 관계도 포함
            $entities = $this->repository->loadRelations([$entity]);
            return $entities[0] ?? $entity;
        }

        return null;
    }

    public function toArray(): array
    {
        return array_map(fn($entity) => $entity->toArray(), $this->get());
    }

    public function toJson(): string
    {
        return json_encode($this->toArray(), JSON_UNESCAPED_UNICODE);
    }

    /**
     * @param Entity[] $entities
     */
    public function deleteForAll(array $entities): int
    {
        if (empty($entities)) {
            return 0;
        }

        $primaryKey = $entities[0]->getPrimaryKeyName();
        
        $ids = array_map(function (Entity $entity) {
            return $entity->getPrimaryKey();
        }, $entities);

        return $this->whereIn($primaryKey, $ids)->delete();
    }

}
