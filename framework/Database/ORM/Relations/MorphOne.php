<?php

namespace Framework\Database\ORM\Relations;

use Framework\Database\ORM\Entities\Entity;
use Framework\Database\ORM\ORM;

/**
 * 다형성 1:1 관계 (ex: User, Post → Image)
 * - 이미지 테이블에 morph_type/morph_id 필드
 */
class MorphOne extends Relation
{
    /** @var string morph_type 컬럼명 */
    protected $morphType;
    /** @var string morph_id 컬럼명 */
    protected $morphId;

    /** @var string 실제 morph type 값 (ex: User::class) */
    protected $typeValue;

    /** @var string 부모(엔티티) PK */
    protected $localKey;

    public function __construct(
        Entity $parent,
        string $relatedEntityClass,
        string $morphType,   // morph_type 컬럼
        string $morphId,     // morph_id 컬럼
        string $localKey,    // 부모 PK
        string $typeValue = null // morph type 값(없으면 부모 클래스)
    ) {
        parent::__construct($parent, $relatedEntityClass);
        $this->morphType = $morphType;
        $this->morphId = $morphId;
        $this->localKey = $localKey;
        $this->typeValue = $typeValue ?: get_class($parent);
    }

    public function addEagerConstraints(array $entities): void
    {
        $ids = array_map(fn($e) => $e->get($this->localKey), $entities);

        $this->query
            ->whereIn($this->morphId, $ids)
            ->where($this->morphType, $this->typeValue);
    }

    public function getEagerResults(array $entities): array
    {
        if (!$this->query) return [];
        return $this->query->get();
    }

    public function match(array $entities, array $results, string $relationName): void
    {
        // morph_id로 결과를 인덱싱
        $grouped = [];
        foreach ($results as $item) {
            $id = $item->get($this->morphId);
            $grouped[$id] = $item;
        }
        foreach ($entities as $entity) {
            $key = $entity->get($this->localKey);
            $entity->{$relationName} = $grouped[$key] ?? null;
        }
    }

    public function getResults()
    {
        $id = $this->parent->get($this->localKey);

        return $this->query
            ->where($this->morphId, $id)
            ->where($this->morphType, $this->typeValue)
            ->first();
    }
}
