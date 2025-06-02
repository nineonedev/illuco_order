<?php

namespace Framework\Database\Relations;

use Framework\Database\Contracts\RepositoryInterface;
use Framework\Database\Entities\Entity;


class HasOneThrough extends Relation
{
    protected string $relatedEntity;
    protected string $throughEntity;
    protected string $throughTable;
    
    protected string $firstKey;         // through 외래키 (related에서 through 참조)
    protected string $secondKey;        // parent 외래키 (through에서 parent 참조)
    protected string $localKey;         // parent PK
    protected string $secondLocalKey;   // through PK

    public function __construct(
        Entity $parent,
        RepositoryInterface $repository,
        string $relatedEntity,
        string $throughEntity,
        string $firstKey,
        string $secondKey,
        string $localKey,
        string $secondLocalKey
    ) {
        $this->relatedEntity = $relatedEntity;
        $this->throughEntity = $throughEntity;
        $this->firstKey = $firstKey;
        $this->secondKey = $secondKey;
        $this->localKey = $localKey;
        $this->secondLocalKey = $secondLocalKey;

        parent::__construct($parent, $repository);

        $relatedTable = (new $relatedEntity())->getTable();
        $this->throughTable = (new $throughEntity())->getTable();

        $this->query
            ->join($this->throughTable, "{$this->throughTable}.{$this->secondKey}", '=', "{$relatedTable}.{$this->firstKey}")
            ->where("{$this->throughTable}.{$this->secondLocalKey}", '=', $parent->get($this->localKey));
    }

    public function get(): ?Entity
    {
        $record = $this->query->first();
        return $record ? $this->repository->createEntity((array) $record) : null;
    }


    public function getEagerResults(array $entities): array
    {
        $localValues = array_map(fn($e) => $e->get($this->localKey), $entities);

        $relatedTable = (new $this->relatedEntity())->getTable();
        $throughTable = (new $this->throughEntity())->getTable();

        $records = $this->repository
            ->getBuilder()
            ->join($throughTable, "{$throughTable}.{$this->secondKey}", '=', "{$relatedTable}.{$this->firstKey}")
            ->whereIn("{$throughTable}.{$this->secondLocalKey}", $localValues)
            ->get();

        $grouped = [];

        foreach ($records as $record) {
            $entity = $this->repository->createEntity((array) $record);
            $key = $record->{$this->secondLocalKey};
            $grouped[$key] = $entity;
        }

        return $grouped;
    }


    protected function getRelatedEntity(): string
    {
        return $this->repository->getEntityClass();
    }
}
