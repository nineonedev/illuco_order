<?php

namespace Framework\Database\ORM\Relations;

use Exception;
use Framework\Database\ORM\Entities\Entity;
use Framework\Database\ORM\RelationMap;

/**
 * 다형성 1:1 관계 (ex: User, Post → Image)
 * - 이미지 테이블에 morph_type/morph_id 필드
 */
class MorphOne extends MorphRelation
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

        // 별칭(예: 'user', 'post')이 있으면 RelationMap에서 가져오고, 없으면 클래스명 사용
        if ($typeValue === null) {
            $this->typeValue = RelationMap::morphAlias(get_class($parent)) ?? get_class($parent)::alias();
        } else {
            $this->typeValue = $typeValue;
        }
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
