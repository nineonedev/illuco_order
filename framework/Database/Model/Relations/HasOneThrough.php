<?php

namespace Framework\Database\Model\Relations;

use Framework\Database\Model\Model;

class HasOneThrough extends Relation
{
    protected string $throughModelClass;
    protected string $throughKey;
    protected string $firstKey;
    protected string $secondKey;
    protected string $localKey;

    public function __construct(
        Model $parentModel,
        string $relatedModelClass,
        string $throughModelClass,
        string $firstKey,   // Country.id → users.country_id
        string $secondKey,  // users.id → posts.user_id
        string $throughKey = 'id',
        string $localKey = 'id'
    ) {
        parent::__construct($parentModel, $relatedModelClass);

        $this->throughModelClass = $throughModelClass;
        $this->firstKey = $firstKey;
        $this->secondKey = $secondKey;
        $this->throughKey = $throughKey;
        $this->localKey = $localKey;

        $this->query = $this->getRelatedModel()->getRepository()->query();
    }

    public function getResults(): ?array
    {
        $relatedTable = $this->getRelatedModel()->getTable();
        $throughTable = (new $this->throughModelClass)->getTable();

        return $this->query
            ->select("{$relatedTable}.*")
            ->join($throughTable, "{$throughTable}.{$this->throughKey}", '=', "{$relatedTable}.{$this->secondKey}")
            ->where("{$throughTable}.{$this->firstKey}", $this->parentModel->get($this->localKey))
            ->first();
    }

    public function addEagerConstraints(array $parents): void
    {
        $relatedTable = $this->getRelatedModel()->getTable();
        $throughTable = (new $this->throughModelClass)->getTable();

        $this->query
            ->select("{$relatedTable}.*", "{$throughTable}.{$this->firstKey} as parent_key")
            ->join($throughTable, "{$throughTable}.{$this->throughKey}", '=', "{$relatedTable}.{$this->secondKey}")
            ->whereIn("{$throughTable}.{$this->firstKey}", $this->getKeys($parents, $this->localKey));
    }

    public function getEagerResults(array $parents): array
    {
        return $this->query->get();
    }

    public function match(array &$parents, array $results, string $relationName): void
    {
        $dictionary = [];

        foreach ($results as $result) {
            $parentKey = $result['parent_key'] ?? null;
            if ($parentKey !== null) {
                $dictionary[$parentKey] = $result;
            }
        }

        foreach ($parents as $parent) {
            $key = $parent->get($this->localKey);
            $parent->setRelation($relationName, $dictionary[$key] ?? null);
        }
    }
}
