<?php

namespace Framework\Database\Relations;

use Framework\Database\Entities\Entity;

class BelongsTo extends Relation
{
    protected string $relatedEntity;
    protected string $foreignKey;
    protected string $ownerKey;

    public function __construct(
        Entity $parent,
        string $relatedEntity,
        string $foreignKey,
        string $ownerKey = 'id'
    ) {
        $this->relatedEntity = $relatedEntity;
        $this->foreignKey = $foreignKey;
        $this->ownerKey = $ownerKey;

        parent::__construct($parent);

        $this->query->where($this->ownerKey, '=', $parent->get($this->foreignKey));
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
        $foreignValues = array_map(fn($e) => $e->get($this->foreignKey), $entities);

        $records = $this->query
            ->whereIn($this->ownerKey, $foreignValues)
            ->get();

        $grouped = [];

        foreach ($records as $record) {
            $entity = $this->repository->createEntity((array) $record);
            $key = $record->{$this->ownerKey};
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

    public function getOwnerKey(): string
    {
        return $this->ownerKey;
    }
}
