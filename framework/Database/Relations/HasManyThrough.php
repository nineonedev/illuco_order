<?php

namespace Framework\Database\Relations;

use Framework\Database\Entities\Entity;

class HasManyThrough extends Relation
{
    protected string $relatedEntity;
    protected string $throughEntity;

    protected string $firstKey;         // related → through FK
    protected string $secondKey;        // through → parent FK
    protected string $localKey;         // parent PK
    protected string $secondLocalKey;   // through PK

    protected string $relatedTable;
    protected string $throughTable;

    public function __construct(
        Entity $parent,
        string $relatedEntity,
        string $throughEntity,
        string $firstKey,
        string $secondKey,
        string $localKey = 'id',
        string $secondLocalKey = 'id'
    ) {
        $this->relatedEntity = $relatedEntity;
        $this->throughEntity = $throughEntity;
        $this->firstKey = $firstKey;
        $this->secondKey = $secondKey;
        $this->localKey = $localKey;
        $this->secondLocalKey = $secondLocalKey;

        parent::__construct($parent);

        $this->relatedTable = (new $relatedEntity)->getTable();
        $this->throughTable = (new $throughEntity)->getTable();

        $this->query
            ->join(
                $this->throughTable,
                "{$this->throughTable}.{$this->secondKey}",
                '=',
                "{$this->relatedTable}.{$this->firstKey}"
            )
            ->where("{$this->throughTable}.{$this->secondLocalKey}", '=', $parent->get($this->localKey));
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
        $localValues = array_map(fn($e) => $e->get($this->localKey), $entities);
        if (empty($localValues)) return [];

        $records = $this->repository
            ->getBuilder()
            ->getConnection()
            ->table($this->relatedTable)
            ->join(
                $this->throughTable,
                "{$this->throughTable}.{$this->secondKey}",
                '=',
                "{$this->relatedTable}.{$this->firstKey}"
            )
            ->whereIn("{$this->throughTable}.{$this->secondLocalKey}", $localValues)
            ->get();

        $grouped = [];

        foreach ($records as $record) {
            $entity = $this->repository->createEntity((array)$record);
            $key = $record->{$this->secondLocalKey};
            $grouped[$key][] = $entity;
        }

        return $grouped;
    }

    protected function getRelatedEntity(): string
    {
        return $this->relatedEntity;
    }
}
