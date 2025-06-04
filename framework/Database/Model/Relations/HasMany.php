<?php

namespace Framework\Database\Model\Relations;

use Framework\Database\Model\Model;

class HasMany extends Relation
{
    protected string $foreignKey;
    protected string $localKey;

    public function __construct(
        Model $parentModel,
        string $relatedModelClass,
        string $foreignKey,
        string $localKey = 'id'
    ) {
        parent::__construct($parentModel, $relatedModelClass);

        $this->foreignKey = $foreignKey;
        $this->localKey = $localKey;
        $this->query = $this->getRelatedModel()->getRepository()->query();
    }

    public function getResults(): array
    {
        return $this->query
            ->where($this->foreignKey, '=', $this->parentModel->get($this->localKey))
            ->get();
    }

    public function addEagerConstraints(array $parents): void
    {
        $localKeys = $this->getKeys($parents, $this->localKey);

        $this->query->whereIn($this->foreignKey, $localKeys);
    }

    public function getEagerResults(array $parents): array
    {
        return $this->query->get();
    }

    public function match(array &$parents, array $results, string $relationName): void
    {
        $dictionary = [];

        foreach ($results as $result) {
            $key = $result[$this->foreignKey] ?? null;
            if ($key !== null) {
                $dictionary[$key][] = $result;
            }
        }

        foreach ($parents as $parent) {
            $key = $parent->get($this->localKey);
            $related = $dictionary[$key] ?? [];
            $parent->setRelation($relationName, $related);
        }
    }
}
