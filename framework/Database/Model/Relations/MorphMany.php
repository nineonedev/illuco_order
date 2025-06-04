<?php

namespace Framework\Database\Model\Relations;

use Framework\Database\Model\Model;

class MorphMany extends Relation
{
    protected string $morphName;
    protected string $typeColumn;
    protected string $idColumn;
    protected string $localKey;

    protected \Framework\Database\Query\Builder $query;

    public function __construct(
        Model $parentModel,
        string $relatedModelClass,
        string $morphName,
        string $localKey = 'id'
    ) {
        parent::__construct($parentModel, $relatedModelClass);

        $this->morphName  = $morphName;
        $this->typeColumn = "{$morphName}_type";
        $this->idColumn   = "{$morphName}_id";
        $this->localKey   = $localKey;

        $this->query = db((new $relatedModelClass)->getTable());
    }

    public function getResults(): array
    {
        return (new $this->relatedModelClass)::where($this->typeColumn, get_class($this->parentModel))
            ->where($this->idColumn, $this->parentModel->get($this->localKey))
            ->get();
    }

    public function addEagerConstraints(array $parents): void
    {
        $this->query
            ->where($this->typeColumn, get_class($this->parentModel))
            ->whereIn($this->idColumn, $this->getKeys($parents, $this->localKey));
    }

    public function getEagerResults(array $parents): array
    {
        return $this->query->get();
    }

    public function match(array &$parents, array $results, string $relationName): void
    {
        $dictionary = [];

        foreach ($results as $result) {
            $key = $result[$this->idColumn] ?? null;
            if ($key !== null) {
                $dictionary[$key][] = $result;
            }
        }

        foreach ($parents as $parent) {
            $key = $parent->get($this->localKey);
            $parent->setRelation($relationName, $dictionary[$key] ?? []);
        }
    }
}
