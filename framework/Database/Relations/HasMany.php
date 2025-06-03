<?php

namespace Framework\Database\Relations;

use Framework\Database\Entities\Entity;
use Framework\Database\Contracts\RepositoryInterface;
use Framework\Database\Repositories\RepositoryResolver;

class HasMany extends Relation
{
    protected string $foreignKey;
    protected string $localKey;
    protected string $relatedEntity;

    public function __construct(
        Entity $parent,
        string $relatedEntity,
        string $foreignKey,
        string $localKey = 'id'
    ) {
        $this->relatedEntity = $relatedEntity;
        $this->foreignKey = $foreignKey;
        $this->localKey = $localKey;

        $repository = RepositoryResolver::resolveFromEntity($relatedEntity);
        parent::__construct($parent, $repository);

        $this->query->where($this->foreignKey, '=', $parent->get($this->localKey));
    }

    public function get(): array
    {
        $records = $this->query->get();

        return array_map(
            fn($record) => $this->repository->createEntity((array) $record),
            $records
        );
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
            $grouped[$key][] = $entity;
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
