<?php

namespace Framework\Database\ORM\Relations;

use Framework\Database\ORM\Entities\Entity;
use Framework\Database\ORM\ORM;

class HasOne extends Relation
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

    /**
     * 여러 부모 엔티티에 대해 eager load시 제약조건 추가 (ex: whereIn)
     */
    public function addEagerConstraints(array $entities): void
    {
        $keys = array_map(fn($e) => $e->get($this->localKey), $entities);
        $this->query
            ->whereIn($this->foreignKey, $keys);
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
     * 결과 매칭 (부모엔티티에 자식엔티티 할당)
     */
    public function match(array $entities, array $results, string $relationName): void
    {
        // results를 FK 기준으로 그룹핑
        $grouped = [];
        foreach ($results as $item) {
            $fk = $item->get($this->foreignKey);
            $grouped[$fk] = $item;
        }

        foreach ($entities as $entity) {
            $key = $entity->get($this->localKey);
            $entity->setRelation($relationName, $grouped[$key] ?? null);
        }
    }

    /**
     * Lazy load: 단일 엔티티의 hasOne 관계를 바로 로드
     */
    public function getResults()
    {
        $fkValue = $this->parent->get($this->localKey);

        $this->query
            ->where($this->foreignKey, $fkValue)
            ->first();
    }
}
