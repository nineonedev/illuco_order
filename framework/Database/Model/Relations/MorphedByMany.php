<?php

namespace Framework\Database\Model\Relations;

use Framework\Database\Model\Entities\Entity;

class MorphedByMany extends Relation
{
    protected string $morphType;
    protected string $morphId;
    protected string $pivotTable;
    protected string $relatedType;
    protected string $relatedKey;
    protected string $parentKey;

    public function __construct(
        Entity $parentEntity,
        string $relatedRepositoryClass,
        string $pivotTable,
        string $morphName,                  // 예: 'taggable'
        string $relatedType,               // 예: Post::class
        string $relatedKey = 'id',
        string $parentKey = 'id'
    ) {
        parent::__construct($parentEntity, $relatedRepositoryClass);

        $this->pivotTable = $pivotTable;
        $this->morphType = $morphName . '_type';
        $this->morphId = $morphName . '_id';
        $this->relatedType = $relatedType;
        $this->relatedKey = $relatedKey;
        $this->parentKey = $parentKey;
    }

    public function getResults(): array
    {
        return $this->getQuery()
            ->join($this->pivotTable, "{$this->relatedRepository->getTable()}.{$this->relatedKey}", '=', "{$this->pivotTable}.{$this->morphId}")
            ->where("{$this->pivotTable}.{$this->morphType}", $this->relatedType)
            ->where("{$this->pivotTable}.{$this->morphId}", $this->parentEntity->get($this->parentKey))
            ->get();
    }

    public function addEagerConstraints(array $entities): void
    {
        $ids = array_map(fn($e) => $e->get($this->parentKey), $entities);

        $this->getQuery()
            ->join($this->pivotTable, "{$this->relatedRepository->getTable()}.{$this->relatedKey}", '=', "{$this->pivotTable}.{$this->morphId}")
            ->where("{$this->pivotTable}.{$this->morphType}", $this->relatedType)
            ->whereIn("{$this->pivotTable}.{$this->morphId}", array_unique($ids));
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
            $key = $result->get($this->morphId);
            $dictionary[$key][] = $result;
        }

        foreach ($entities as $entity) {
            $key = $entity->get($this->parentKey);
            $entity->setRelation($relationName, $dictionary[$key] ?? []);
        }
    }

    public function initRelation(): array
    {
        return [];
    }
}
