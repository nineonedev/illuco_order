<?php

namespace Framework\Database\ORM\Relations;

use Exception;
use Framework\Database\ORM\Entities\Entity;
use Framework\Database\ORM\RelationMap;

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
    /** @var string morph_type에 저장될 값 (별칭 또는 클래스명) */
    protected $typeValue;
    /** @var string 부모(엔티티) PK 컬럼명 */
    protected $localKey;

    public function __construct(
        Entity $parent,
        string $relatedEntityClass,
        string $morphType = 'morph_type',
        string $morphId = 'morph_id',
        string $localKey = 'id',
        $typeValue = null // morph_type에 저장할 값(별칭 또는 클래스명)
    ) {
        parent::__construct($parent, $relatedEntityClass);
        $this->morphType = $morphType;
        $this->morphId = $morphId;
        $this->localKey = $localKey;
        $this->typeValue = $typeValue ?? get_class($parent)::alias();
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
            $entity->setRelation($relationName, $grouped[$key] ?? null);
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
