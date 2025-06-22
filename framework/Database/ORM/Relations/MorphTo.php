<?php

namespace Framework\Database\ORM\Relations;

use Framework\Database\ORM\Entities\Entity;
use Framework\Database\ORM\Rel;
use Framework\Database\Query\Builder;

/**
 * 다형성 역방향 관계 (ex: Image → Post/User 등 morph 대상)
 * - morph_type, morph_id 컬럼을 가진 엔티티에서 'owner' 엔티티 로드
 */
class MorphTo extends Relation
{
    protected $morphType;        // morph_type 컬럼명
    protected $morphId;          // morph_id 컬럼명
    protected array $typesMap = [];   // [alias => class]
    protected array $typeGroups = []; // [alias => [id, id], ...]

    public function __construct(
        Entity $parent,
        string $morphType = 'morph_type',
        string $morphId = 'morph_id',
        array $typesMap = []
    ) {
        parent::__construct($parent, get_class($parent));
        $this->morphType = $morphType;
        $this->morphId = $morphId;
        $this->typesMap = $typesMap ?: Rel::morphableMap();
    }

    public function getRelatedQuery(): Builder
    {
        throw new \LogicException("MorphTo 관계는 getRelatedQuery()를 사용할 수 없습니다.");
    }

    public function addExistsConstraints(Builder $relatedQuery, Builder $parentQuery): void
    {
        throw new \LogicException("MorphTo 관계는 addExistsConstraints()를 지원하지 않습니다.");
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

        // [admin => [1,2,3], employee => [2,3,4]]
        $this->typeGroups = $typeGroups;
    }

    public function getEagerResults(array $entities): array
    {
        if (empty($this->typeGroups)) return [];

        $results = [];
        foreach ($this->typeGroups as $typeValue => $ownerIds) {
            // 1. 엔티티 클래스 찾기 (MorphMap > typesMap)
            $relatedClass = $this->typesMap[$typeValue] ?? null;

            if (!$relatedClass) continue;

            // 2. PK 컬럼명 찾기
            $pk = $relatedClass::make()->getPrimaryKeyName() ?? 'id';

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

        $relatedClass = $this->typesMap[$typeValue] ?? null;

        if (!$relatedClass || !$ownerId) return null;

        $pk = $relatedClass::make()->getPrimaryKeyName() ?? 'id';

        return $this->queryForEntity($relatedClass)
            ->where($pk, $ownerId)
            ->first();
    }
}
