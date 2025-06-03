<?php

namespace Framework\Database\Model\Relations;

use Framework\Database\Model\Entities\Entity;
use Framework\Database\Contracts\RepositoryInterface;

class MorphedByMany extends Relation
{
    protected string $pivotTable;
    protected string $typeColumn;
    protected string $idColumn;
    protected string $pivotRelatedKey;   // ex: post_id
    protected string $relatedKey;        // ex: id (primary key of Post)
    protected string $morphType;         // ex: Post::class

    protected array $eagerTargetIds = [];

    public function __construct(
        Entity $parent,
        RepositoryInterface $repository,
        string $pivotTable,
        string $pivotRelatedKey,
        string $typeColumn,
        string $idColumn,
        string $relatedKey = 'id'
    ) {
        parent::__construct($parent, $repository);

        $this->pivotTable = $pivotTable;
        $this->pivotRelatedKey = $pivotRelatedKey;
        $this->typeColumn = $typeColumn;
        $this->idColumn = $idColumn;
        $this->relatedKey = $relatedKey;
        $this->morphType = get_class($parent);
    }

    public function getResults(): array
    {
        $id = $this->parent->get($this->parent->getPrimaryKeyName());

        if (!$id) return [];

        return $this->repository
            ->join($this->pivotTable, $this->repository->getTable().'.'.$this->relatedKey, '=', $this->pivotTable.'.'.$this->pivotRelatedKey)
            ->where($this->pivotTable.'.'.$this->typeColumn, $this->morphType)
            ->where($this->pivotTable.'.'.$this->idColumn, $id)
            ->get();
    }

    public function initRelation(array $entities, string $relation): array
    {
        foreach ($entities as $entity) {
            $entity->set($relation, []);
        }

        return $entities;
    }

    public function addEagerConstraints(array $entities): void
    {
        $this->eagerTargetIds = array_map(fn($e) => $e->get($e->getPrimaryKeyName()), $entities);
    }

    public function getEagerResults(array $entities): array
    {
        if (empty($this->eagerTargetIds)) {
            return [];
        }

        return $this->repository
            ->join($this->pivotTable, $this->repository->getTable().'.'.$this->relatedKey, '=', $this->pivotTable.'.'.$this->pivotRelatedKey)
            ->where($this->pivotTable.'.'.$this->typeColumn, $this->morphType)
            ->whereIn($this->pivotTable.'.'.$this->idColumn, $this->eagerTargetIds)
            ->get();
    }

    public function match(array $entities, array $results, string $relation): array
    {
        $dictionary = [];

        foreach ($results as $result) {
            $ownerId = $result->get($this->pivotTable.'.'.$this->idColumn);
            $dictionary[$ownerId][] = $result;
        }

        foreach ($entities as $entity) {
            $id = $entity->get($entity->getPrimaryKeyName());
            $entity->set($relation, $dictionary[$id] ?? []);
        }

        return $entities;
    }

    protected function getKeys(array $entities): array
    {
        return array_map(fn($e) => $e->get($e->getPrimaryKeyName()), $entities);
    }
}
