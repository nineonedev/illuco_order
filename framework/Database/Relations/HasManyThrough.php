<?php

namespace Framework\Database\Relations;

use Framework\Database\Contracts\RepositoryInterface;
use Framework\Database\Entities\Entity;

class HasManyThrough extends Relation
{
    protected string $throughEntity;
    protected string $throughTable;

    protected string $firstKey;         // related → through FK
    protected string $secondKey;        // through → parent FK
    protected string $localKey;         // parent PK
    protected string $secondLocalKey;   // through PK

    public function __construct(
        Entity $parent,
        RepositoryInterface $repository,
        string $throughEntity,
        string $firstKey,
        string $secondKey,
        string $localKey,
        string $secondLocalKey
    ) {
        $this->throughEntity = $throughEntity;
        $this->firstKey = $firstKey;
        $this->secondKey = $secondKey;
        $this->localKey = $localKey;
        $this->secondLocalKey = $secondLocalKey;

        parent::__construct($parent, $repository);

        $relatedTable = $this->repository->getTable();
        $this->throughTable = (new $throughEntity())->getTable();

        $this->query
            ->join($this->throughTable, "{$this->throughTable}.{$this->secondKey}", '=', "{$relatedTable}.{$this->firstKey}")
            ->where("{$this->throughTable}.{$this->secondLocalKey}", '=', $parent->get($this->localKey));
    }

    public function get(): array
    {
        $records = $this->query->get();
        return array_map(fn($row) => $this->repository->createEntity((array)$row), $records);
    }

    public function getEagerResults(array $entities): array
    {
        $localIds = array_map(fn($e) => $e->get($this->localKey), $entities);
        if (empty($localIds)) return [];

        $relatedTable = $this->repository->getTable();
        $throughTable = $this->throughTable;

        $records = $this->repository
            ->getBuilder()
            ->getConnection()
            ->table($relatedTable)
            ->join($throughTable, "{$throughTable}.{$this->secondKey}", '=', "{$relatedTable}.{$this->firstKey}")
            ->whereIn("{$throughTable}.{$this->secondLocalKey}", $localIds)
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
        return $this->repository->getEntityClass();
    }
}
