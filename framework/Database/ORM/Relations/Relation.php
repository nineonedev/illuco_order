<?php

namespace Framework\Database\ORM\Relations;

use Framework\Database\ORM\Entities\Entity;
use Framework\Database\Query\Builder;
use Framework\Database\Query\EntityQueryBuilder;

abstract class Relation
{
    /** @var Entity 부모 엔티티 */
    protected $parent;

    /** @var string 관련(타겟) 엔티티 클래스명 */
    protected $relatedEntityClass;

    protected Builder $query;

    /**
     * @param class-string<Entity> $relatedEntityClass
     */
    public function __construct(Entity $parent, string $relatedEntityClass)
    {
        $this->parent = $parent;
        $this->relatedEntityClass = $relatedEntityClass;
        $this->query = $this->queryForEntity($relatedEntityClass);
    }

    public function getRelatedEntityClass(): string
    {
        return $this->relatedEntityClass;
    }

    protected function queryForEntity(?string $entityClass = null): EntityQueryBuilder
    {
        if (is_null($entityClass)) {
            $entityClass = $this->relatedEntityClass;
        }
        
        $class = $entityClass::repositoryClass();
        $repository = (new $class);
        
        return (new $repository())->query();
    }

    /**
     * Eager Load: 여러 엔티티 대상 제약조건 추가
     * @param Entity[] $entities
     * @return void
     */
    abstract public function addEagerConstraints(array $entities): void;

    /**
     * Eager Load: 결과 쿼리
     * @param Entity[] $entities
     * @return array
     */
    abstract public function getEagerResults(array $entities): array;

    /**
     * 매칭 (eager load된 결과를 각 엔티티에 할당)
     * @param Entity[] $entities
     * @param array $results
     * @param string $relationName
     * @return void
     */
    abstract public function match(array $entities, array $results, string $relationName): void;

    /**
     * Lazy Load: 단일 엔티티에서 on-demand로 로드
     * @return mixed
     */
    abstract public function getResults();
}
