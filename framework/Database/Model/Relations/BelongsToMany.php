<?php

namespace Framework\Database\Model\Relations;

use Framework\Database\Model\Model;

class BelongsToMany extends Relation
{
    protected string $pivotTable;
    protected string $foreignPivotKey;
    protected string $relatedPivotKey;
    protected string $parentKey;
    protected string $relatedKey;
    protected array $pivotColumns = [];

    public function __construct(
        Model $parentModel,
        string $relatedModelClass,
        string $pivotTable,
        string $foreignPivotKey,  // user_id
        string $relatedPivotKey,  // role_id
        string $parentKey = 'id',
        string $relatedKey = 'id'
    ) {
        parent::__construct($parentModel, $relatedModelClass);

        $this->pivotTable = $pivotTable;
        $this->foreignPivotKey = $foreignPivotKey;
        $this->relatedPivotKey = $relatedPivotKey;
        $this->parentKey = $parentKey;
        $this->relatedKey = $relatedKey;

        $this->query = $this->getRelatedModel()->getRepository()->query();
    }

    public function withPivot(...$columns): self
    {
        $this->pivotColumns = array_merge($this->pivotColumns, $columns);
        return $this;
    }

    public function getResults(): array
    {
        $relatedTable = $this->getRelatedModel()->getTable();
        $columns = ["{$relatedTable}.*"];

        foreach ($this->pivotColumns as $col) {
            $columns[] = "{$this->pivotTable}.{$col} as pivot_{$col}";
        }

        return $this->query
            ->select($columns)
            ->join($this->pivotTable, "{$relatedTable}.{$this->relatedKey}", '=', "{$this->pivotTable}.{$this->relatedPivotKey}")
            ->where("{$this->pivotTable}.{$this->foreignPivotKey}", $this->parentModel->get($this->parentKey))
            ->get();
    }

    public function getEagerResults(array $parents): array
    {
        $relatedTable = $this->getRelatedModel()->getTable();
        $columns = ["{$relatedTable}.*"];

        foreach ($this->pivotColumns as $col) {
            $columns[] = "{$this->pivotTable}.{$col} as pivot_{$col}";
        }

        return $this->query->select($columns)->get();
    }


    public function addEagerConstraints(array $parents): void
    {
        $relatedTable = $this->getRelatedModel()->getTable();

        $this->query
            ->join($this->pivotTable, "{$relatedTable}.{$this->relatedKey}", '=', "{$this->pivotTable}.{$this->relatedPivotKey}")
            ->whereIn("{$this->pivotTable}.{$this->foreignPivotKey}", $this->getKeys($parents, $this->parentKey));
    }
    public function match(array &$parents, array $results, string $relationName): void
    {
        $dictionary = [];

        foreach ($results as $result) {
            $key = $result[$this->foreignPivotKey];
            $dictionary[$key][] = $result;
        }

        foreach ($parents as $parent) {
            $key = $parent->get($this->parentKey);
            $parent->setRelation($relationName, $dictionary[$key] ?? []);
        }
    }

    public function attach($ids, array $pivotData = []): void
    {
        $ids = is_array($ids) ? $ids : [$ids];
        $rows = [];

        foreach ($ids as $id) {
            $row = array_merge($pivotData, [
                $this->foreignPivotKey => $this->parentModel->get($this->parentKey),
                $this->relatedPivotKey => $id,
            ]);
            $rows[] = $row;
        }

        db($this->pivotTable)->insert($rows);
    }

    public function detach($ids = null): void
    {
        $query = db($this->pivotTable)
            ->where($this->foreignPivotKey, '=', $this->parentModel->get($this->parentKey));

        if ($ids !== null) {
            $ids = is_array($ids) ? $ids : [$ids];
            $query->whereIn($this->relatedPivotKey, $ids);
        }

        $query->delete();
    }

    public function sync(array $ids, bool $detaching = true): void
    {
        $current = array_values(
            db($this->pivotTable)
            ->where($this->foreignPivotKey, '=', $this->parentModel->get($this->parentKey))
            ->pluck($this->relatedPivotKey)
        );

        $detach = array_diff($current, $ids);
        $attach = array_diff($ids, $current);

        if ($detaching && count($detach)) {
            $this->detach($detach);
        }

        if (count($attach)) {
            $this->attach($attach);
        }
    }
}
