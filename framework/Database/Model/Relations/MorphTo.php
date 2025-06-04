<?php

namespace Framework\Database\Model\Relations;

use Framework\Database\Model\Model;
use Framework\Support\Collection;

class MorphTo extends Relation
{
    protected string $typeColumn;
    protected string $idColumn;

    public function __construct(
        Model $parentModel,
        string $morphName
    ) {
        parent::__construct($parentModel, '');

        $this->typeColumn = "{$morphName}_type";
        $this->idColumn   = "{$morphName}_id";
    }

    public function getResults(): ?Model
    {
        $type = $this->parentModel->get($this->typeColumn);
        $id   = $this->parentModel->get($this->idColumn);

        if (! $type || ! $id) {
            return null;
        }

        /** @var Model $related */
        $related = new $type();

        return $type::find($id);
    }

    public function addEagerConstraints(array $parents): void
    {
        // MorphTo는 다형성 관계이기 때문에
        // 타입별로 따로 쿼리를 구성해야 함
    }

    public function getEagerResults(array $parents): array
    {
        $grouped = [];

        foreach ($parents as $parent) {
            $type = $parent->get($this->typeColumn);
            $id   = $parent->get($this->idColumn);

            if ($type && $id) {
                $grouped[$type][] = $id;
            }
        }

        $results = [];

        foreach ($grouped as $type => $ids) {
            /** @var Model $related */
            $related = new $type();

            $models = $type::whereIn($related->getPrimaryKey(), array_unique($ids));
            foreach ($models as $model) {
                $key = $model->get($related->getPrimaryKey());
                $results[$type][$key] = $model;
            }
        }

        return $results;
    }

    public function match(array &$parents, array $results, string $relationName): void
    {
        foreach ($parents as $parent) {
            $type = $parent->get($this->typeColumn);
            $id   = $parent->get($this->idColumn);

            $model = $results[$type][$id] ?? null;
            $parent->setRelation($relationName, $model);
        }
    }
}
