<?php

namespace Framework\Database\Relations;

use Framework\Database\Entities\Entity;

class MorphedByMany extends Relation
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
                "SELECT {$this->relatedPivotKey} FROM {$this->pivotTable} WHERE {$this->morphTypeColumn} = ? AND {$this->morphIdColumn} = ?",
                [$type, $id]
            );

        $ids = array_map(fn($row) => $row->{$this->relatedPivotKey}, $pivotRows);

        if (empty($ids)) return [];

        $records = $this->query->whereIn($this->foreignPivotKey, $ids)->get();

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

        $relatedIds = array_map(fn($r) => $r->{$this->relatedPivotKey}, $pivotRows);

        $records = $this->query->whereIn($this->foreignPivotKey, $relatedIds)->get();

        $relatedEntities = [];
        foreach ($records as $record) {
            $relatedEntities[$record->{$this->foreignPivotKey}] = $this->repository->createEntity((array)$record);
        }

        $results = [];
        foreach ($pivotRows as $pivot) {
            $morphId = $pivot->{$this->morphIdColumn};
            $relatedId = $pivot->{$this->relatedPivotKey};

            $results[$morphId][] = $relatedEntities[$relatedId] ?? null;
        }

        return $results;
    }

    protected function getRelatedEntity(): string
    {
        return $this->relatedEntity;
    }
}
