<?php

namespace Framework\Database\Relations;

use Framework\Database\Entities\Entity;

class HasOne extends Relation
{
    protected string $relatedEntity;
    protected string $foreignKey;
    protected string $localKey;

    public function __construct(
        Entity $parent,
        string $relatedEntity,
        string $foreignKey,
        string $localKey = 'id'
    ) {
        $this->relatedEntity = $relatedEntity;
        $this->foreignKey = $foreignKey;
        $this->localKey = $localKey;

        parent::__construct($parent);

        $this->query->where($this->foreignKey, '=', $parent->get($this->localKey));
    }

    public function get(): ?Entity
    {
        $record = $this->query->first();

        return $record
            ? $this->repository->createEntity((array) $record)
            : null;
    }

    public function getEagerResults(array $entities): array
    {
        $localValues = array_map(fn($e) => $e->get($this->localKey), $entities);

        $records = $this->query
            ->whereIn($this->foreignKey, $localValues)
            ->get();

        $grouped = [];

        foreach ($records as $record) {
            $entity = $this->repository->createEntity((array) $record);
            $key = $record->{$this->foreignKey};
            $grouped[$key] = $entity;
        }

        return $grouped;
    }

    protected function getRelatedEntity(): string
    {
        return $this->relatedEntity;
    }

    public function getForeignKey(): string
    {
        return $this->foreignKey;
    }

    public function getLocalKey(): string
    {
        return $this->localKey;
    }
}
