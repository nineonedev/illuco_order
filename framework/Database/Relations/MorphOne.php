<?php

namespace Framework\Database\Relations;

use Framework\Database\Contracts\RepositoryInterface;
use Framework\Database\Entities\Entity;

class MorphOne extends Relation
{
    protected string $morphName;
    protected string $localKey;

    protected string $morphTypeColumn;
    protected string $morphIdColumn;

    public function __construct(
        Entity $parent,
        RepositoryInterface $repository,
        string $morphName,
        string $localKey = 'id'
    ) {
        $this->morphName = $morphName;
        $this->localKey = $localKey;

        $this->morphTypeColumn = "{$morphName}_type";
        $this->morphIdColumn = "{$morphName}_id";

        parent::__construct($parent, $repository);

        $this->query
            ->where($this->morphTypeColumn, '=', get_class($parent))
            ->where($this->morphIdColumn, '=', $parent->get($this->localKey));
    }

    public function get(): ?Entity
    {
        $record = $this->query->first();

        if (!$record) return null;

        return $this->repository->createEntity((array)$record);
    }

    public function getEagerResults(array $entities): array
    {
        $type = get_class($this->parent);
        $ids = array_map(fn($e) => $e->get($this->localKey), $entities);

        $records = $this->query
            ->whereIn($this->morphIdColumn, $ids)
            ->where($this->morphTypeColumn, '=', $type)
            ->get();

        $grouped = [];

        foreach ($records as $record) {
            $entity = $this->repository->createEntity((array)$record);
            $key = $record->{$this->morphIdColumn};
            $grouped[$key] = $entity;
        }

        return $grouped;
    }


    protected function getRelatedEntity(): string
    {
        return $this->repository->getEntityClass();
    }
}
