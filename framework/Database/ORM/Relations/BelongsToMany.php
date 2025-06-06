<?php

namespace Framework\Database\ORM\Relations;

use Framework\Database\ORM\Entities\Entity;
use Framework\Database\ORM\ORM;
use Framework\Database\ORM\Relations\Pivot;

class BelongsToMany extends Relation implements Pivotable
{
    /** @var string 중간(Pivot) 테이블 */
    protected $pivotTable;
    /** @var string 현재(부모) 엔티티에서 Pivot으로의 FK */
    protected $foreignKey;
    /** @var string 타겟(related) 엔티티에서 Pivot으로의 FK */
    protected $relatedKey;
    /** @var string Pivot → 타겟 테이블의 PK */
    protected $relatedEntityPrimaryKey;

    public function __construct(
        Entity $parent,
        string $relatedEntityClass,
        string $pivotTable,
        string $foreignKey,
        string $relatedKey,
        string $relatedEntityPrimaryKey = 'id'
    ) {
        parent::__construct($parent, $relatedEntityClass);
        $this->pivotTable = $pivotTable;
        $this->foreignKey = $foreignKey;
        $this->relatedKey = $relatedKey;
        $this->relatedEntityPrimaryKey = $relatedEntityPrimaryKey;
    }

    /**
     * Eager load: 여러 엔티티(부모) → Pivot → 관련 엔티티 한 번에 join
     */
    public function addEagerConstraints(array $entities): void
    {
        $parentKeys = array_map(fn($e) => $e->get($e->getPrimaryKeyName()), $entities);

        $this->query
            ->join(
                $this->pivotTable,
                "{$this->relatedEntityClass::table()}.{$this->relatedEntityPrimaryKey}", '=', "{$this->pivotTable}.{$this->relatedKey}"
            )
            ->whereIn("{$this->pivotTable}.{$this->foreignKey}", $parentKeys);
    }

    /**
     * Eager load 결과 fetch
     */
    public function getEagerResults(array $entities): array
    {
        if (!$this->query) return [];
        return $this->query->get();
    }

    /**
     * 부모 엔티티에 관련 자식 엔티티 배열로 할당
     */
    public function match(array $entities, array $results, string $relationName): void
    {
        // Pivot의 FK로 그룹핑
        $grouped = [];
        foreach ($results as $item) {
            // Pivot의 FK 컬럼 값 추출
            $pivotFk = $item->{$this->pivotTable}[$this->foreignKey] ?? null;
            if ($pivotFk !== null) {
                $grouped[$pivotFk][] = $item;
            }
        }
        foreach ($entities as $entity) {
            $key = $entity->get($entity->getPrimaryKeyName());
            $entity->{$relationName} = $grouped[$key] ?? [];
        }
    }

    /**
     * Lazy load: 한 부모의 관련 자식 전체 반환
     */
    public function getResults()
    {
        $parentKey = $this->parent->get($this->parent->getPrimaryKeyName());

        return $this->query
            ->join(
                $this->pivotTable,
                "{$this->relatedEntityClass::table()}.{$this->relatedEntityPrimaryKey}", '=', "{$this->pivotTable}.{$this->relatedKey}"
            )
            ->where("{$this->pivotTable}.{$this->foreignKey}", $parentKey)
            ->get();
    }

    // ----- Pivot Attach/Detach/Sync -----
    public function attach(Entity $related, array $attributes = []): bool
    {
        $pivot = new Pivot();
        $pivot->setTable($this->pivotTable);
        $pivot->setPivotKeys($this->foreignKey, $this->relatedKey);
        return $pivot->attach($this->parent, $related, $attributes);
    }

    public function detach(array $relatedIds = []): int
    {
        $pivot = new Pivot();
        $pivot->setTable($this->pivotTable);
        $pivot->setPivotKeys($this->foreignKey, $this->relatedKey);
        return $pivot->detach($this->parent, $relatedIds);
    }

    public function sync(array $relatedData): void
    {
        $pivot = new Pivot();
        $pivot->setTable($this->pivotTable);
        $pivot->setPivotKeys($this->foreignKey, $this->relatedKey);
        $pivot->sync($this->parent, $relatedData);
    }
}
