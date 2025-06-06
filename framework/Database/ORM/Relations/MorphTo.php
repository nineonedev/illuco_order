<?php

namespace Framework\Database\ORM\Relations;

use Framework\Database\ORM\Entities\Entity;
use Framework\Database\ORM\ORM;

/**
 * 다형성 역방향 (ex: Image → Post/User 등 morph 대상)
 * - morph_type, morph_id 컬럼을 가진 엔티티에서 'owner' 엔티티 로드
 */
class MorphTo extends Relation
{
    protected $morphType;  // morph_type 컬럼명
    protected $morphId;    // morph_id 컬럼명
    protected array $typesMap;   // morph_type값 → 엔티티 클래스 (ex: ['post' => Post::class])
    protected array $typeFieldMap; // (선택) morph_type 실제 값과 매핑
    protected array $typeGroups = [];

    public function __construct(
        Entity $parent,
        string $morphType,      // morph_type 컬럼
        string $morphId,        // morph_id 컬럼
        array $typesMap,        // morph_type값 → 엔티티 클래스 (ex: ['user' => User::class, 'post' => Post::class])
        array $typeFieldMap = [] // (옵션) morph_type 실제 값 → repo class or entity class
    ) {
        // relatedEntityClass는 다형적이므로 부모에서 null로 넘김
        parent::__construct($parent, null);
        $this->morphType = $morphType;
        $this->morphId = $morphId;
        $this->typesMap = $typesMap;
        $this->typeFieldMap = $typeFieldMap;
    }

    /**
     * MorphTo는 일반적으로 Eager load보다 Lazy 상황에서 많이 사용
     */
    public function addEagerConstraints(array $entities): void
    {
        // 엔티티들을 morph_type 별로 그룹핑해서
        // 각 morph_type별로 whereIn 쿼리 후 결과 합치기!
        // (고급 usecase는 생략, 실전용 확장만 필요시 추가)
        // ... (skip)

        // morph_type 별로 엔티티 분류
        $typeGroups = [];
        foreach ($entities as $entity) {
            $typeValue = $entity->get($this->morphType);
            $ownerId = $entity->get($this->morphId);

            if ($typeValue && $ownerId) {
                $typeGroups[$typeValue][] = $ownerId;
            }
        }
        $this->typeGroups = $typeGroups; // 후처리를 위해 보관
    }

    public function getEagerResults(array $entities): array
    {
        // (복잡한 case: 각 morph_type별 쿼리 분기 후 결과 합치기)
        // ... (skip)

        if (empty($this->typeGroups)) return [];
    
        $results = [];

        foreach ($this->typeGroups as $typeValue => $ownerIds) {
            $relatedClass = $this->typesMap[$typeValue] ?? null;
            if (!$relatedClass) continue;
            $rows = $this->queryForEntity($relatedClass)
                ->whereIn($relatedClass::primaryKeyName(), $ownerIds)
                ->get();
            // 타입별로 분리해서 저장
            foreach ($rows as $row) {
                $results[$typeValue][$row->get($relatedClass::primaryKeyName())] = $row;
            }
        }
        
        return $results;
    }

    public function match(array $entities, array $results, string $relationName): void
    {
        // 복잡한 case: skip (실제 상황에서만 구현)
        foreach ($entities as $entity) {
            $typeValue = $entity->get($this->morphType);
            $ownerId   = $entity->get($this->morphId);
            $entity->{$relationName} = $results[$typeValue][$ownerId] ?? null;
        }
    }

    /**
     * Lazy Load: 단일 엔티티의 morphTo owner 로딩
     */
    public function getResults()
    {
        $typeValue = $this->parent->get($this->morphType);
        $ownerId   = $this->parent->get($this->morphId);

        // typeValue로 실제 entity/repo class 추론
        $relatedClass = $this->typesMap[$typeValue] ?? null;

        if (!$relatedClass || !$ownerId) return null;

        // ORM에서 해당 엔티티의 레포지토리 쿼리로 PK 탐색
        return $this->query
            ->where($relatedClass::primaryKeyName(), $ownerId)
            ->first();
    }
}
