<?php

namespace Framework\Database\Model\Relations;

use Framework\Database\Model\Entities\Entity;

class HasOneThrough extends Relation
{
    protected string $throughRepositoryClass;
    protected string $relatedRepositoryClass;

    protected string $firstKey;     // ex: country.id → users.country_id
    protected string $secondKey;    // ex: users.id → posts.user_id
    protected string $localKey;     // ex: country.id

    public function __construct(
        Entity $parentEntity,
        string $relatedRepositoryClass,
        string $throughRepositoryClass,
        string $firstKey,
        string $secondKey,
        string $localKey = 'id'
    ) {
        parent::__construct($parentEntity, $relatedRepositoryClass);

        $this->throughRepositoryClass = $throughRepositoryClass;
        $this->relatedRepositoryClass = $relatedRepositoryClass;

        $this->firstKey = $firstKey;
        $this->secondKey = $secondKey;
        $this->localKey = $localKey;
    }

    public function getResults(): array
    {
        $throughRepo = new $this->throughRepositoryClass;
        $throughTable = $throughRepo->getTable();
        $relatedTable = $this->relatedRepository->getTable();

        $parentKeyValue = $this->parentEntity->get($this->localKey);

        $query = $this->getQuery()
            ->join($throughTable, "{$throughTable}.{$this->secondKey}", '=', "{$relatedTable}.{$this->firstKey}")
            ->where("{$throughTable}.{$this->localKey}", $parentKeyValue)
            ->limit(1);

        $row = $query->first();
        return $row ? [$this->relatedRepository->toEntities((array)$row)] : [];
    }

    public function addEagerConstraints(array $entities): void
    {
        $keys = array_map(fn($e) => $e->get($this->localKey), $entities);
        $this->getQuery()->whereIn("{$this->throughRepositoryClass}." . $this->localKey, array_unique($keys));
    }

    public function getEagerResults(array $entities): array
    {
        $throughRepo = new $this->throughRepositoryClass;
        $throughTable = $throughRepo->getTable();
        $relatedTable = $this->relatedRepository->getTable();

        $parentKeys = array_map(fn($e) => $e->get($this->localKey), $entities);

        $query = $this->getQuery()
            ->join($throughTable, "{$throughTable}.{$this->secondKey}", '=', "{$relatedTable}.{$this->firstKey}")
            ->whereIn("{$throughTable}.{$this->localKey}", array_unique($parentKeys));

        $rows = $query->get();
        return $this->relatedRepository->toEntities($rows);
    }

    public function match(array &$entities, array $results, string $relationName): void
    {
        $dictionary = [];

        foreach ($results as $result) {
            $throughKey = $result->get($this->localKey);
            $dictionary[$throughKey] = $result;
        }

        foreach ($entities as $entity) {
            $key = $entity->get($this->localKey);
            $entity->setRelation($relationName, $dictionary[$key] ?? null);
        }
    }

    public function initRelation(): ?Entity
    {
        return null;
    }
}
