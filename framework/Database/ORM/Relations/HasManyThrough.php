<?php

namespace Framework\Database\ORM\Relations;

use Framework\Database\ORM\Entities\Entity;
use Framework\Database\ORM\ORM;
use Framework\Database\Query\Builder;

class HasManyThrough extends Relation
{
    /** @var string 중간(Through) 엔티티 클래스 */
    protected $throughEntityClass;
    /** @var string 중간 테이블의 FK (부모 PK → through FK) */
    protected $firstKey;
    /** @var string 자식 테이블의 FK (through PK → 자식 FK) */
    protected $secondKey;
    /** @var string 부모 PK */
    protected $localKey;

    public function __construct(
        Entity $parent,
        string $relatedEntityClass,
        string $throughEntityClass,
        string $firstKey,    // ex) countries.id → users.country_id
        string $secondKey,   // ex) users.id → posts.user_id
        string $localKey     // ex) countries PK
    ) {
        parent::__construct($parent, $relatedEntityClass);
        $this->throughEntityClass = $throughEntityClass;
        $this->firstKey = $firstKey;
        $this->secondKey = $secondKey;
        $this->localKey = $localKey;
    }

    public function getRelatedQuery(): Builder
    {
        return $this->query;
    }

    public function addExistsConstraints(Builder $relatedQuery, Builder $parentQuery): void
    {
        $throughTable = $this->throughEntityClass::table();
        $relatedTable = $relatedQuery->getTable();

        $relatedQuery
            ->join(
                $throughTable,
                "{$throughTable}.{$this->firstKey}",
                '=',
                "{$relatedTable}.{$this->secondKey}"
            )
            ->whereColumn(
                "{$throughTable}.{$this->firstKey}",
                '=',
                $parentQuery->getTable() . '.' . $this->localKey
            );
    }


    public function addEagerConstraints(array $entities): void
    {
        $parentKeys = array_map(fn($e) => $e->get($this->localKey), $entities);

        $throughTable = $this->throughEntityClass::table();
        $relatedTable = $this->relatedEntityClass::table();

        $this->query
            ->join(
                $throughTable,
                "{$throughTable}.{$this->firstKey}", '=', "{$relatedTable}.{$this->secondKey}"
            )
            ->whereIn("{$throughTable}.{$this->firstKey}", $parentKeys);
    }

    public function getEagerResults(array $entities): array
    {
        if (!$this->query) return [];
        return $this->query->get();
    }

    public function match(array $entities, array $results, string $relationName): void
    {
        $grouped = [];
        foreach ($results as $item) {
            $fk = $item->{$this->firstKey} ?? null;
            if ($fk !== null) {
                $grouped[$fk][] = $item;
            }
        }

        foreach ($entities as $entity) {
            $key = $entity->get($this->localKey);
            $existing = $entity->getRelation($relationName);
            $current = $grouped[$key] ?? [];

            if (is_array($existing)) {
                $entity->setRelation($relationName, array_merge($existing, $current));
            } else {
                $entity->setRelation($relationName, $current);
            }
        }
    }

    public function getResults()
    {
        $parentKey = $this->parent->get($this->localKey);

        $throughTable = $this->throughEntityClass::table();
        $relatedTable = $this->relatedEntityClass::table();

        $this->query
            ->join(
                $throughTable,
                "{$throughTable}.{$this->firstKey}", '=', "{$relatedTable}.{$this->secondKey}"
            )
            ->where("{$throughTable}.{$this->firstKey}", $parentKey)
            ->get();
    }
}
