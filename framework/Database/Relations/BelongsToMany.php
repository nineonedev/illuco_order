<?php

namespace Framework\Database\Relations;

use Framework\Database\Contracts\RepositoryInterface;
use Framework\Database\Entities\Entity;

class BelongsToMany extends Relation
{
    protected string $pivotTable;
    protected string $foreignPivotKey;
    protected string $relatedPivotKey;
    protected string $parentKey;
    protected string $relatedKey;

    public function __construct(
        Entity $parent,
        RepositoryInterface $repository,
        string $pivotTable,
        string $foreignPivotKey,
        string $relatedPivotKey,
        string $parentKey = 'id',
        string $relatedKey = 'id'
    ) {
        $this->pivotTable = $pivotTable;
        $this->foreignPivotKey = $foreignPivotKey;
        $this->relatedPivotKey = $relatedPivotKey;
        $this->parentKey = $parentKey;
        $this->relatedKey = $relatedKey;

        parent::__construct($parent, $repository);
    }

    public function get(): array
    {
        $parentId = $this->parent->get($this->parentKey);

        $pivotRows = $this->repository
            ->getBuilder()
            ->getConnection()
            ->select(
                "SELECT {$this->foreignPivotKey} FROM {$this->pivotTable} WHERE {$this->relatedPivotKey} = ?",
                [$parentId]
            );

        $ids = array_map(fn($row) => $row->{$this->foreignPivotKey}, $pivotRows);

        if (empty($ids)) return [];

        $records = $this->query->whereIn($this->relatedKey, $ids)->get();

        $entityClass = $this->repository->getEntityClass();
        return array_map(fn($r) => new $entityClass((array)$r), $records);
    }

    public function getEagerResults(array $entities): array
    {
        $parentIds = array_map(fn($e) => $e->get($this->parentKey), $entities);

        $placeholders = implode(',', array_fill(0, count($parentIds), '?'));
        $bindings = $parentIds;

        $pivotRows = $this->repository
            ->getBuilder()
            ->getConnection()
            ->select(
                "SELECT * FROM {$this->pivotTable} WHERE {$this->relatedPivotKey} IN ({$placeholders})",
                $bindings
            );

        $foreignIds = array_map(fn($r) => $r->{$this->foreignPivotKey}, $pivotRows);

        $records = $this->query->whereIn($this->relatedKey, $foreignIds)->get();
        $entityClass = $this->repository->getEntityClass();

        $relatedEntities = [];
        foreach ($records as $record) {
            $relatedEntities[$record->{$this->relatedKey}] = new $entityClass((array)$record);
        }

        $results = [];
        foreach ($pivotRows as $pivot) {
            $relatedId = $pivot->{$this->relatedPivotKey};
            $foreignId = $pivot->{$this->foreignPivotKey};

            $results[$relatedId][] = $relatedEntities[$foreignId] ?? null;
        }

        return $results;
    }

    public function attach($ids): void
    {
        $relatedIds = is_array($ids) ? $ids : [$ids];
        $parentId = $this->parent->get($this->parentKey);

        foreach ($relatedIds as $id) {
            $this->repository
                ->getBuilder()
                ->getConnection()
                ->statement(
                    "INSERT IGNORE INTO {$this->pivotTable} ({$this->foreignPivotKey}, {$this->relatedPivotKey}) VALUES (?, ?)",
                    [$id, $parentId]
                );
        }
    }

    public function detach($ids = null): void
    {
        $parentId = $this->parent->get($this->parentKey);
        $connection = $this->repository->getBuilder()->getConnection();

        if ($ids === null) {
            $connection->statement(
                "DELETE FROM {$this->pivotTable} WHERE {$this->relatedPivotKey} = ?",
                [$parentId]
            );
            return;
        }

        $relatedIds = is_array($ids) ? $ids : [$ids];
        $placeholders = implode(',', array_fill(0, count($relatedIds), '?'));
        $bindings = array_merge($relatedIds, [$parentId]);

        $connection->statement(
            "DELETE FROM {$this->pivotTable} WHERE {$this->foreignPivotKey} IN ({$placeholders}) AND {$this->relatedPivotKey} = ?",
            $bindings
        );
    }

    public function sync(array $ids): void
    {
        $parentId = $this->parent->get($this->parentKey);
        $connection = $this->repository->getBuilder()->getConnection();

        $pivotRows = $connection->select(
            "SELECT {$this->foreignPivotKey} FROM {$this->pivotTable} WHERE {$this->relatedPivotKey} = ?",
            [$parentId]
        );

        $existing = array_map(fn($r) => $r->{$this->foreignPivotKey}, $pivotRows);

        $toAttach = array_diff($ids, $existing);
        $toDetach = array_diff($existing, $ids);

        $this->attach($toAttach);
        $this->detach($toDetach);
    }

    protected function getRelatedEntity(): string
    {
        return $this->repository->getEntityClass();
    }
}
