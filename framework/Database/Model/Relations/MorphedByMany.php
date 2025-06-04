<?php

namespace Framework\Database\Model\Relations;

use Framework\Database\Model\Model;

class MorphedByMany extends Relation
{
    protected string $relatedModelClass;
    protected string $morphName;
    protected string $pivotTable;
    protected string $localKey;
    protected string $relatedKey;
    protected string $morphTypeColumn;
    protected string $morphIdColumn;

    protected array $pivotColumns = [];

    protected \Framework\Database\Query\Builder $query;

    public function __construct(
        Model $parentModel,
        string $relatedModelClass,
        string $morphName,
        string $pivotTable,
        string $localKey = 'id',
        string $relatedKey = 'id'
    ) {
        parent::__construct($parentModel, $relatedModelClass);

        $this->relatedModelClass = $relatedModelClass;
        $this->morphName         = $morphName;
        $this->pivotTable        = $pivotTable;
        $this->localKey          = $localKey;
        $this->relatedKey        = $relatedKey;
        $this->morphTypeColumn   = "{$morphName}_type";
        $this->morphIdColumn     = "{$morphName}_id";

        $this->query = db((new $relatedModelClass)->getTable());
    }

    public function getResults(): array
    {
        $relatedTable = (new $this->relatedModelClass)->getTable();
        $columns = ["{$relatedTable}.*"];

        foreach ($this->pivotColumns as $col) {
            $columns[] = "{$this->pivotTable}.{$col} as pivot_{$col}";
        }

        return db($relatedTable)
            ->select($columns)
            ->join($this->pivotTable, "{$relatedTable}.{$this->relatedKey}", '=', "{$this->pivotTable}.related_id")
            ->where("{$this->pivotTable}.{$this->morphIdColumn}", $this->parentModel->get($this->localKey))
            ->where("{$this->pivotTable}.{$this->morphTypeColumn}", get_class($this->parentModel))
            ->get();
    }

    public function getEagerResults(array $parents): array
    {
        $relatedTable = (new $this->relatedModelClass)->getTable();
        $columns = ["{$relatedTable}.*"];

        foreach ($this->pivotColumns as $col) {
            $columns[] = "{$this->pivotTable}.{$col} as pivot_{$col}";
        }

        return $this->query->select($columns)->get();
    }


    public function addEagerConstraints(array $parents): void
    {
        $relatedTable = (new $this->relatedModelClass)->getTable();
        $ids = $this->getKeys($parents, $this->localKey);

        $this->query
            ->join($this->pivotTable, "{$relatedTable}.{$this->relatedKey}", '=', "{$this->pivotTable}.related_id")
            ->where("{$this->pivotTable}.{$this->morphTypeColumn}", get_class($this->parentModel))
            ->whereIn("{$this->pivotTable}.{$this->morphIdColumn}", $ids);
    }

    public function match(array &$parents, array $results, string $relationName): void
    {
        $dictionary = [];

        foreach ($results as $result) {
            $morphId = $result[$this->morphIdColumn] ?? null;

            if ($morphId !== null) {
                $dictionary[$morphId][] = $result;
            }
        }

        foreach ($parents as $parent) {
            $key = $parent->get($this->localKey);
            $parent->setRelation($relationName, $dictionary[$key] ?? []);
        }
    }

    public function attach($relatedIds, array $pivotData = []): void
    {
        $relatedIds = is_array($relatedIds) ? $relatedIds : [$relatedIds];
        $rows = [];

        foreach ($relatedIds as $id) {
            $rows[] = array_merge($pivotData, [
                $this->relatedKey        => $id,
                $this->morphTypeColumn   => get_class($this->parentModel),
                $this->morphIdColumn     => $this->parentModel->get($this->localKey),
            ]);
        }

        db($this->pivotTable)->insert($rows);
    }


    public function detach($relatedIds = null): void
    {
        $query = db($this->pivotTable)
            ->where($this->morphTypeColumn, get_class($this->parentModel))
            ->where($this->morphIdColumn, $this->parentModel->get($this->localKey));

        if ($relatedIds !== null) {
            $relatedIds = is_array($relatedIds) ? $relatedIds : [$relatedIds];
            $query->whereIn($this->relatedKey, $relatedIds);
        }

        $query->delete();
    }


    public function sync(array $relatedIds): void
    {
        $existing = array_values(
            db($this->pivotTable)
            ->where($this->morphTypeColumn, get_class($this->parentModel))
            ->where($this->morphIdColumn, $this->parentModel->get($this->localKey))
            ->pluck($this->relatedKey)
        );

        $toDelete = array_diff($existing, $relatedIds);
        $toInsert = array_diff($relatedIds, $existing);

        if (!empty($toDelete)) {
            $this->detach($toDelete);
        }

        if (!empty($toInsert)) {
            $this->attach($toInsert);
        }
    }

    public function withPivot(...$columns): self
    {
        $this->pivotColumns = array_merge($this->pivotColumns, $columns);
        return $this;
    }

}
