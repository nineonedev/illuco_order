<?php

namespace Framework\Database\ORM\Relations;

use Framework\Database\ORM\Entities\Entity;
use Framework\Database\Query\Builder;

class BelongsTo extends Relation
{
    /** @var string $foreignKey 현재(자식) 엔티티에 있는 FK */
    protected $foreignKey;
    /** @var string $ownerKey 부모(타겟) 엔티티의 PK(또는 유니크키) */
    protected $ownerKey;

    public function __construct(Entity $parent, string $relatedEntityClass, string $foreignKey, string $ownerKey)
    {
        parent::__construct($parent, $relatedEntityClass);
        $this->foreignKey = $foreignKey;
        $this->ownerKey = $ownerKey;
    }

    /**
     * 여러 자식(현 엔티티)들에 대해 eager load시 제약조건 추가 (whereIn)
     */
    public function addEagerConstraints(array $entities): void
    {
        $fks = array_filter(array_map(fn($e) => $e->get($this->foreignKey), $entities));
        $this->query->whereIn($this->ownerKey, $fks);
    }

    /**
     * Eager Load: 결과 fetch
     */
    public function getEagerResults(array $entities): array
    {
        if (!$this->query) return [];
        return $this->query->get();
    }

    /**
     * 결과 매칭 (자식 엔티티에 부모 엔티티 할당)
     */
    public function match(array $entities, array $results, string $relationName): void
    {
        // results를 ownerKey 기준으로 맵핑
        $grouped = [];
        foreach ($results as $item) {
            $pk = $item->get($this->ownerKey);
            $grouped[$pk] = $item;
        }

        foreach ($entities as $entity) {
            $fk = $entity->get($this->foreignKey);

            // 기존 값이 있더라도 항상 덮어쓰되, null은 무시 가능
            if (array_key_exists($fk, $grouped)) {
                $entity->setRelation($relationName, $grouped[$fk]);
            } else {
                $entity->setRelation($relationName, null);
            }
        }
    }

    public function getRelatedQuery(): Builder
    {
        return $this->query;
    }

    public function addExistsConstraints(Builder $relatedQuery, Builder $parentQuery): void
    {
        $relatedQuery->whereColumn(
            $this->ownerKey,
            '=',
            $parentQuery->getTable() . '.' . $this->foreignKey
        );
    }
    
    /**
     * Lazy load: 단일 엔티티에서 바로 부모 로드
     */
    public function getResults()
    {
        $fkValue = $this->parent->get($this->foreignKey);

        return $this->query
            ->where($this->ownerKey, $fkValue)
            ->first();
    }
}
