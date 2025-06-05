<?php

namespace Framework\Database\Model\Relations;

use Framework\Database\Model\Entities\Entity;

class MorphToMany extends Relation
{
    protected string $pivotTable;
    protected string $morphType;   // 예: commentable_type
    protected string $morphId;     // 예: commentable_id
    protected string $relatedId;   // 예: tag_id (pivot의 외래키)
    protected string $relatedKey;  // 예: id (related entity의 기본키)
    protected string $typeClass;   // 예: Post::class

    public function __construct(
        Entity $parentEntity,
        string $relatedRepositoryClass,
        string $pivotTable,
        string $morphType,
        string $morphId,
        string $relatedId,
        string $relatedKey,
        string $typeClass
    ) {
        parent::__construct($parentEntity, $relatedRepositoryClass);

        $this->pivotTable = $pivotTable;
        $this->morphType = $morphType;
        $this->morphId = $morphId;
        $this->relatedId = $relatedId;
        $this->relatedKey = $relatedKey;
        $this->typeClass = $typeClass;
    }

    public function getResults(): array
    {
        $parentId = $this->parentEntity->getPrimaryKey();

        $rows = $this->getQuery()
            ->join($this->pivotTable, "{$this->pivotTable}.{$this->relatedId}", '=', $this->relatedRepository->getTable() . '.' . $this->relatedKey)
            ->where("{$this->pivotTable}.{$this->morphId}", $parentId)
            ->where("{$this->pivotTable}.{$this->morphType}", $this->typeClass)
            ->get();

        return $this->relatedRepository->toEntities($rows);
    }

    public function addEagerConstraints(array $entities): void
    {
        $parentIds = array_map(fn($entity) => $entity->getPrimaryKey(), $entities);

        $this->getQuery()
            ->join($this->pivotTable, "{$this->pivotTable}.{$this->relatedId}", '=', $this->relatedRepository->getTable() . '.' . $this->relatedKey)
            ->whereIn("{$this->pivotTable}.{$this->morphId}", $parentIds)
            ->where("{$this->pivotTable}.{$this->morphType}", $this->typeClass);
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
            $pivot = $result->getAttributes();
            $parentId = $pivot[$this->morphId] ?? null;

            if ($parentId !== null) {
                $dictionary[$parentId][] = $result;
            }
        }

        foreach ($entities as $entity) {
            $key = $entity->getPrimaryKey();
            $entity->setRelation($relationName, $dictionary[$key] ?? []);
        }
    }

    public function initRelation(): array
    {
        return [];
    }
}
