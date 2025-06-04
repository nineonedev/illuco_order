<?php

namespace Framework\Database\Model\Relations;

use Framework\Database\Model\Model;

class BelongsTo extends Relation
{
    protected string $foreignKey;
    protected string $ownerKey;

    public function __construct(
        Model $parentModel,
        string $relatedModelClass,
        string $foreignKey,
        string $ownerKey = 'id'
    ) {
        parent::__construct($parentModel, $relatedModelClass);

        $this->foreignKey = $foreignKey;
        $this->ownerKey = $ownerKey;
        $this->query = $this->getRelatedModel()->getRepository()->query();
    }

    public function getResults(): ?Model
    {
        $foreignKeyValue = $this->parentModel->get($this->foreignKey);

        return $this->query
            ->where($this->ownerKey, '=', $foreignKeyValue)
            ->first();
    }

    public function addEagerConstraints(array $parents): void
    {
        $foreignKeys = $this->getKeys($parents, $this->foreignKey);

        $this->query->whereIn($this->ownerKey, $foreignKeys);
    }

    public function getEagerResults(array $parents): array
    {
        return $this->query->get();
    }

    public function match(array &$parents, array $results, string $relationName): void
    {
        $dictionary = [];

        foreach ($results as $result) {
            $key = $result[$this->ownerKey] ?? null;
            if ($key !== null) {
                $dictionary[$key] = $result;
            }
        }

        foreach ($parents as $parent) {
            $foreignKey = $parent->get($this->foreignKey);
            $parent->setRelation($relationName, $dictionary[$foreignKey] ?? null);
        }
    }
}
