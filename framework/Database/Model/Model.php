<?php

namespace Framework\Database\Model;

use Framework\Database\Contracts\RepositoryInterface;
use Framework\Database\Model\Entities\Entity;
use Framework\Database\Model\Relations\Relation;
use Framework\Support\Str;

abstract class Model
{
    protected array $with = [];

    /**
     * 사용할 테이블 이름
     */
    public function getTable(): string
    {
        return $this->makeRepository()->getTable();
    }

    /**
     * 기본 PK 이름
     */
    public function getPrimaryKey(): string
    {
        return $this->makeEntity()->getPrimaryKeyName();
    }

    /**
     * 연관 Entity 클래스
     */
    abstract public function entityClass(): string;

    /**
     * 연관 Repository 클래스
     */
    abstract public function repositoryClass(): string;

    /**
     * @return Entity
     */
    public function makeEntity(array $data = []): Entity
    {
        $class = $this->entityClass();
        return new $class($data);
    }

    /**
     * @return RepositoryInterface
     */
    public function makeRepository(): RepositoryInterface
    {
        $class = $this->repositoryClass();
        return app($class); // DI Container
    }

    /**
     * Eager load 대상 지정
     */
    public function with(array $relations): self
    {
        $this->with = $relations;
        return $this;
    }

    public function getEagerRelations(): array
    {
        return $this->with;
    }
}
