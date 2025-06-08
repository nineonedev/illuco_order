<?php

namespace Framework\Database\ORM\Relations;

use Framework\Database\ORM\Entities\Entity;
use Framework\Database\ORM\RelationMap;

/**
 * 다형성 역방향 관계 (ex: Image → Post/User 등 morph 대상)
 * - morph_type, morph_id 컬럼을 가진 엔티티에서 'owner' 엔티티 로드
 */
class MorphTo extends Relation
{
    protected $morphType;        // morph_type 컬럼명
    protected $morphId;          // morph_id 컬럼명
    protected array $typesMap;   // morph_type값 → 엔티티 클래스
    protected array $typeGroups = [];

    public function __construct(
        Entity $parent,
        string $morphType = 'morph_type',
        string $morphId = 'morph_id',
        array $typesMap = []
    ) {
        parent::__construct($parent, null);
        $this->morphType = $morphType;
        $this->morphId = $morphId;

        // 전역 MorphMap 우선, 없으면 인스턴스별 typesMap
        $this->typesMap = !empty(RelationMap::morphMap()) ? RelationMap::morphMap() : $typesMap;
    }

    public function addEagerConstraints(array $entities): void
    {
        $typeGroups = [];
        foreach ($entities as $entity) {
            $typeValue = $entity->get($this->morphType);
            $ownerId = $entity->get($this->morphId);

            if ($typeValue && $ownerId) {
                $typeGroups[$typeValue][] = $ownerId;
            }
        }
        $this->typeGroups = $typeGroups;
    }

    public function getEagerResults(array $entities): array
    {
        if (empty($this->typeGroups)) return [];

        $results = [];
        foreach ($this->typeGroups as $typeValue => $ownerIds) {
            // 1. 엔티티 클래스 찾기 (MorphMap > typesMap)
            $relatedClass =
                RelationMap::resolveMorph($typeValue)
                ?? ($this->typesMap[$typeValue] ?? null);

            if (!$relatedClass) continue;

            // 2. PK 컬럼명 찾기
            $pk = method_exists($relatedClass, 'primaryKeyName')
                ? $relatedClass::primaryKeyName()
                : 'id';

            $rows = $this->queryForEntity($relatedClass)
                ->whereIn($pk, $ownerIds)
                ->get();

            foreach ($rows as $row) {
                $rowPk = is_callable([$row, 'get']) ? $row->get($pk) : $row->$pk;
                $results[$typeValue][$rowPk] = $row;
            }
        }
        return $results;
    }

    public function match(array $entities, array $results, string $relationName): void
    {
        foreach ($entities as $entity) {
            $typeValue = $entity->get($this->morphType);
            $ownerId = $entity->get($this->morphId);

            $entity->setRelation($relationName, $results[$typeValue][$ownerId] ?? null);
        }
    }

    public function getResults()
    {
        $typeValue = $this->parent->get($this->morphType);
        $ownerId = $this->parent->get($this->morphId);

        $relatedClass =
            RelationMap::resolveMorph($typeValue)
            ?? ($this->typesMap[$typeValue] ?? null);

        if (!$relatedClass || !$ownerId) return null;

        $pk = method_exists($relatedClass, 'primaryKeyName')
            ? $relatedClass::primaryKeyName()
            : 'id';

        return $this->queryForEntity($relatedClass)
            ->where($pk, $ownerId)
            ->first();
    }
}
