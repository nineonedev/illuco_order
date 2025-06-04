<?php

namespace Framework\Database\Model\Relations;

use Framework\Database\Model\Model;
use Framework\Database\Query\Builder;

abstract class Relation
{
    protected Model $parentModel;
    protected string $relatedModelClass;
    protected ?Builder $query = null;

    public function __construct(Model $parentModel, string $relatedModelClass)
    {
        $this->parentModel = $parentModel;
        $this->relatedModelClass = $relatedModelClass;
        $this->query = $this->getRelatedModel()->getRepository()->query();
    }

    /**
     * 관계된 모델 인스턴스 반환
     */
    public function getRelatedModel(): Model
    {
        return new $this->relatedModelClass();
    }

    /**
     * 부모 모델 반환
     */
    public function getParentModel(): Model
    {
        return $this->parentModel;
    }

    /**
     * 부모 모델에서 키 추출
     */
    protected function getKeys(array $models, string $key): array
    {
        return array_values(array_unique(array_map(fn($model) => $model->get($key), $models)));
    }

    /**
     * 관계형 쿼리 실행
     */
    abstract public function getResults(): mixed;

    /**
     * Eager Loading 제약조건 추가
     */
    abstract public function addEagerConstraints(array $parents): void;

    /**
     * Eager Loading 결과 조회
     */
    abstract public function getEagerResults(array $parents): array;

    /**
     * Eager Loading 결과 병합
     */
    abstract public function match(array &$parents, array $results, string $relationName): void;
}
