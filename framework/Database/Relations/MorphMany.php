<?php

namespace Framework\Database\Relations;

use Framework\Database\Entities\Entity;

class MorphMany extends Relation
{
    protected string $relatedEntity;
    protected string $morphName;
    protected string $localKey;

    protected string $morphTypeColumn;
    protected string $morphIdColumn;

    public function __construct(
        Entity $parent,
        string $relatedEntity,
        string $morphName,
        string $localKey = 'id'
    ) {
        $this->relatedEntity = $relatedEntity;
        $this->morphName = $morphName;
        $this->localKey = $localKey;

        $this->morphTypeColumn = "{$morphName}_type";
        $this->morphIdColumn = "{$morphName}_id";

        parent::__construct($parent);

        $this->query
            ->where($this->morphTypeColumn, '=', get_class($parent))
            ->where($this->morphIdColumn, '=', $parent->get($this->localKey));
    }

    public function get(): array
    {
        $records = $this->query->get();

        return array_map(
            fn($record) => $this->repository->createEntity((array)$record),
            $records
        );
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
            $grouped[$key][] = $entity;
        }

        return $grouped;
    }

    protected function getRelatedEntity(): string
    {
        return $this->relatedEntity;
    }
}
