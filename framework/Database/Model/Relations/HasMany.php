<?php 

namespace Framework\Database\Model\Relations;

use Framework\Database\Model\Entities\Entity;

class HasMany extends Relation
{
    protected string $foreignKey;
    protected string $localKey;

    public function __construct(Entity $parentEntity, string $relatedRepository, string $foreignKey, string $localKey)
    {
        parent::__construct($parentEntity, $relatedRepository);
        $this->foreignKey = $foreignKey;
        $this->localKey = $localKey;
    }

    public function getResults(): array
    {
        $rows = $this->getQuery()
            ->where($this->foreignKey, $this->parentEntity->get($this->localKey))
            ->get();

        return $this->relatedRepository->toEntities($rows);
    }

    public function addEagerConstraints(array $entities): void
    {
        $keys = array_map(fn($entity) => $entity->get($this->localKey), $entities);
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
            $dictionary[$key][] = $result;
        }

        foreach ($entities as $entity) {
            $key = $entity->get($this->localKey);
            $entity->setRelation($relationName, $dictionary[$key] ?? []);
        }
    }

    public function initRelation(): array
    {
        return [];
    }
}
