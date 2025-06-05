<?php

namespace Framework\Database\Model\Relations;

use Framework\Database\Contracts\RepositoryInterface;
use Framework\Database\Model\Entities\Entity;
use Framework\Database\Query\Builder;

abstract class Relation
{
    protected Entity $parentEntity;
    protected RepositoryInterface $relatedRepository;

    protected ?string $relationName = null;

    public function __construct(Entity $parentEntity, string $relatedRepositoryClass)
    {
        $this->parentEntity = $parentEntity;
        $this->relatedRepository = new $relatedRepositoryClass;
    }

    /**
     * 현재 관계를 위한 쿼리 빌더 반환
     */
    public function getQuery(): Builder
    {
        return $this->relatedRepository->query();
    }

    /**
     * 부모 엔티티 반환
     */
    public function getParentEntity(): Entity
    {
        return $this->parentEntity;
    }

    /**
     * 연관 Repository 반환
     */
    public function getRelatedRepository(): RepositoryInterface
    {
        return $this->relatedRepository;
    }

    /**
     * 관계 이름 설정 (eager loader에서 주입)
     */
    public function setRelationName(string $name): self
    {
        $this->relationName = $name;
        return $this;
    }

    /**
     * 관계 이름 반환
     */
    public function getRelationName(): ?string
    {
        return $this->relationName;
    }

    /**
     * Lazy Load: Entity에 대한 결과 반환
     */
    abstract public function getResults(): array;

    /**
     * Eager Load: 부모들에 대한 조건 설정
     */
    abstract public function addEagerConstraints(array $entities): void;

    /**
     * Eager Load: 조건에 맞는 결과 목록 반환
     */
    abstract public function getEagerResults(array $entities): array;

    /**
     * Eager Load: 결과를 각 Entity에 주입
     */
    abstract public function match(array &$entities, array $results, string $relationName): void;

    /**
     * Eager Load: 초기화용 빈 값 반환 (HasMany는 [], HasOne은 null 등)
     */
    abstract public function initRelation();
}
