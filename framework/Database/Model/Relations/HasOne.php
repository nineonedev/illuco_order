<?php

namespace Framework\Database\Model\Relations;

use Framework\Database\Model\Entities\Entity;

class HasOne extends Relation
{
    protected string $foreignKey;
    protected string $localKey;

    public function __construct(
        Entity $parentEntity,
        string $relatedRepositoryClass,
        string $foreignKey,
        string $localKey
    ) {
        parent::__construct($parentEntity, $relatedRepositoryClass);
        $this->foreignKey = $foreignKey;
        $this->localKey = $localKey;
    }

    /**
     * Lazy load
     */
    public function getResults(): array
    {
        $row = $this->getQuery()
            ->where($this->foreignKey, $this->parentEntity->get($this->localKey))
            ->first();

        return $row ? [$this->relatedRepository->toEntity((array) $row)] : [];
    }

    public function addEagerConstraints(array $entities): void
    {
        $keys = array_map(fn($e) => $e->get($this->localKey), $entities);
        $this->getQuery()->whereIn($this->foreignKey, array_unique($keys));
    }

    public function getEagerResults(array $entities): array
    {
        $rows = $this->getQuery()->get();
        return $this->relatedRepository->toEntities($rows);
    }

    public function match(array &$entities, array $results, string $relationName): void
    {
        $dictionary = [];

        foreach ($results as $result) {
            $key = $result->get($this->foreignKey);
            $dictionary[$key] = $result;
        }

        foreach ($entities as $entity) {
            $value = $dictionary[$entity->get($this->localKey)] ?? null;
            $entity->setRelation($relationName, $value);
        }
    }

    public function initRelation(): ?Entity
    {
        return null;
    }
}
