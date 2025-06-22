<?php

namespace Framework\Database\ORM\Relations;

use Framework\Database\ORM\Entities\Entity;
use Framework\Database\Query\Builder;

class HasMany extends Relation
{
    /** @var string $foreignKey 자식 테이블에 있는 FK */
    protected $foreignKey;
    /** @var string $localKey 부모 엔티티의 PK(또는 유니크키) */
    protected $localKey;

    public function __construct(Entity $parent, string $relatedEntityClass, string $foreignKey, string $localKey)
    {
        parent::__construct($parent, $relatedEntityClass);
        $this->foreignKey = $foreignKey;
        $this->localKey = $localKey;
    }

    public function getRelatedQuery(): Builder
    {
        return $this->query;
    }

    public function addExistsConstraints(Builder $relatedQuery, Builder $parentQuery): void
    {
        $relatedQuery->whereColumn(
            $this->foreignKey,
            '=',
            $parentQuery->getTable() . '.' . $this->localKey
        );
    }


    /**
     * 여러 부모 엔티티에 대해 eager load시 제약조건 추가 (whereIn)
     */
    public function addEagerConstraints(array $entities): void
    {
        $keys = array_map(fn($e) => $e->get($this->localKey), $entities);
        $this->query->whereIn($this->foreignKey, $keys);
    }

    /**
     * Eager Load: 결과 fetch
     */
    public function getEagerResults(array $entities): array
    {
        if (!$this->query) return [];

        $results = $this->query->get();

        return $results;
    }

    /**
     * 결과 매칭 (부모엔티티에 자식엔티티 배열 할당)
     */
    public function match(array $entities, array $results, string $relationName): void
    {
        // 자식들을 FK 기준으로 그룹핑 (부모 PK별)
        $grouped = [];
        foreach ($results as $item) {
            $fk = $item->get($this->foreignKey);
            
            $grouped[$fk][] = $item;
        }

        foreach ($entities as $entity) {
            $key = $entity->get($this->localKey);

            $existing = $entity->getRelation($relationName);
            $current = $grouped[$key] ?? [];

            if (is_array($existing)) {
                // 기존 값과 병합
                $entity->setRelation($relationName, array_merge($existing, $current));
            } else {
                // 기존 값 없으면 그대로 설정
                $entity->setRelation($relationName, $current);
            }
        }
    }


    /**
     * Lazy load: 단일 엔티티의 hasMany 관계를 바로 로드
     */
    public function getResults()
    {
        $fkValue = $this->parent->get($this->localKey);
        
        return $this->query
            ->where($this->foreignKey, $fkValue)
            ->get();
    }
}
