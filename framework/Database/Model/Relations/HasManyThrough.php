<?php

namespace Framework\Database\Model\Relations;

use Framework\Database\Model\Model;

class HasManyThrough extends Relation
{
    protected string $throughModelClass;
    protected string $firstKey;
    protected string $secondKey;
    protected string $throughLocalKey;
    protected string $localKey;

    public function __construct(
        Model $parentModel,
        string $relatedModelClass,
        string $throughModelClass,
        string $firstKey,           // Country.id → users.country_id
        string $secondKey,          // users.id → posts.user_id
        string $throughLocalKey = 'id',
        string $localKey = 'id'
    ) {
        parent::__construct($parentModel, $relatedModelClass);

        $this->throughModelClass = $throughModelClass;
        $this->firstKey          = $firstKey;
        $this->secondKey         = $secondKey;
        $this->throughLocalKey   = $throughLocalKey;
        $this->localKey          = $localKey;

        $this->query = $this->getRelatedModel()->getRepository()->query();
    }

    public function getResults(): array
    {
        $relatedTable = $this->getRelatedModel()->getTable();
        $throughTable = (new $this->throughModelClass)->getTable();

        return $this->query
            ->select("{$relatedTable}.*")
            ->join($throughTable, "{$throughTable}.{$this->throughLocalKey}", '=', "{$relatedTable}.{$this->secondKey}")
            ->where("{$throughTable}.{$this->firstKey}", $this->parentModel->get($this->localKey))
            ->get();
    }

    public function addEagerConstraints(array $parents): void
    {
        $relatedTable = $this->getRelatedModel()->getTable();
        $throughTable = (new $this->throughModelClass)->getTable();

        $this->query
            ->select("{$relatedTable}.*", "{$throughTable}.{$this->firstKey} as parent_key")
            ->join($throughTable, "{$throughTable}.{$this->throughLocalKey}", '=', "{$relatedTable}.{$this->secondKey}")
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
            $key = $result['parent_key'] ?? null;
            if ($key !== null) {
                $dictionary[$key][] = $result;
            }
        }

        foreach ($parents as $parent) {
            $parentKey = $parent->get($this->localKey);
            $parent->setRelation($relationName, $dictionary[$parentKey] ?? []);
        }
    }
}
