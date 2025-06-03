<?php

namespace Framework\Database\Relations;

use Framework\Database\Entities\Entity;

class MorphToMany extends Relation
{
    protected string $relatedEntity;
    protected string $pivotTable;
    protected string $foreignPivotKey;
    protected string $relatedPivotKey;

    protected string $morphTypeColumn;
    protected string $morphIdColumn;

    public function __construct(
        Entity $parent,
        string $relatedEntity,
        string $pivotTable,
        string $foreignPivotKey,
        string $relatedPivotKey,
        string $morphName
    ) {
        $this->relatedEntity = $relatedEntity;
        $this->pivotTable = $pivotTable;
        $this->foreignPivotKey = $foreignPivotKey;
        $this->relatedPivotKey = $relatedPivotKey;

        $this->morphTypeColumn = "{$morphName}_type";
        $this->morphIdColumn = "{$morphName}_id";

        parent::__construct($parent);
    }

    public function get(): array
    {
        $type = get_class($this->parent);
        $id = $this->parent->get('id');

        $pivotRows = $this->repository
            ->getBuilder()
            ->getConnection()
            ->select(
                "SELECT {$this->foreignPivotKey} FROM {$this->pivotTable} WHERE {$this->morphTypeColumn} = ? AND {$this->morphIdColumn} = ?",
                [$type, $id]
            );

        $ids = array_map(fn($row) => $row->{$this->foreignPivotKey}, $pivotRows);

        if (empty($ids)) return [];

        $records = $this->query->whereIn($this->relatedPivotKey, $ids)->get();

        return array_map(
            fn($r) => $this->repository->createEntity((array)$r),
            $records
        );
    }

    public function getEagerResults(array $entities): array
    {
        $type = get_class($this->parent);
        $ids = array_map(fn($e) => $e->get('id'), $entities);

        $placeholders = implode(',', array_fill(0, count($ids), '?'));

        $pivotRows = $this->repository
            ->getBuilder()
            ->getConnection()
            ->select(
                "SELECT * FROM {$this->pivotTable} WHERE {$this->morphTypeColumn} = ? AND {$this->morphIdColumn} IN ({$placeholders})",
                array_merge([$type], $ids)
            );

        $foreignIds = array_map(fn($r) => $r->{$this->foreignPivotKey}, $pivotRows);

        $records = $this->query->whereIn($this->relatedPivotKey, $foreignIds)->get();

        $relatedEntities = [];
        foreach ($records as $record) {
            $relatedEntities[$record->{$this->relatedPivotKey}] = $this->repository->createEntity((array)$record);
        }

        $results = [];
        foreach ($pivotRows as $pivot) {
            $morphId = $pivot->{$this->morphIdColumn};
            $foreignId = $pivot->{$this->foreignPivotKey};

            $results[$morphId][] = $relatedEntities[$foreignId] ?? null;
        }

        return $results;
    }

    public function attach($ids): void
    {
        $type = get_class($this->parent);
        $id = $this->parent->get('id');
        $relatedIds = is_array($ids) ? $ids : [$ids];

        $connection = $this->repository->getBuilder()->getConnection();

        foreach ($relatedIds as $rid) {
            $connection->statement(
                "INSERT IGNORE INTO {$this->pivotTable} ({$this->foreignPivotKey}, {$this->morphTypeColumn}, {$this->morphIdColumn}) VALUES (?, ?, ?)",
                [$rid, $type, $id]
            );
        }
    }

    public function detach($ids = null): void
    {
        $type = get_class($this->parent);
        $id = $this->parent->get('id');
        $connection = $this->repository->getBuilder()->getConnection();

        if ($ids === null) {
            $connection->statement(
                "DELETE FROM {$this->pivotTable} WHERE {$this->morphTypeColumn} = ? AND {$this->morphIdColumn} = ?",
                [$type, $id]
            );
            return;
        }

        $relatedIds = is_array($ids) ? $ids : [$ids];
        $placeholders = implode(',', array_fill(0, count($relatedIds), '?'));

        $connection->statement(
            "DELETE FROM {$this->pivotTable} WHERE {$this->foreignPivotKey} IN ({$placeholders}) AND {$this->morphTypeColumn} = ? AND {$this->morphIdColumn} = ?",
            array_merge($relatedIds, [$type, $id])
        );
    }

    public function sync(array $ids): void
    {
        $type = get_class($this->parent);
        $id = $this->parent->get('id');

        $connection = $this->repository->getBuilder()->getConnection();

        $pivotRows = $connection->select(
            "SELECT {$this->foreignPivotKey} FROM {$this->pivotTable} WHERE {$this->morphTypeColumn} = ? AND {$this->morphIdColumn} = ?",
            [$type, $id]
        );

        $existing = array_map(fn($r) => $r->{$this->foreignPivotKey}, $pivotRows);

        $toAttach = array_diff($ids, $existing);
        $toDetach = array_diff($existing, $ids);

        $this->attach($toAttach);
        $this->detach($toDetach);
    }

    protected function getRelatedEntity(): string
    {
        return $this->relatedEntity;
    }
}
