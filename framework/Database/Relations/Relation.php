<?php 

namespace Framework\Database\Relations;

use Framework\Database\Contracts\RepositoryInterface;
use Framework\Database\Entities\Entity;
use Framework\Database\Query\Builder;

abstract class Relation 
{
    protected Entity $parent; 
    protected Builder $query;
    protected RepositoryInterface $repository; 

    public function __construct(Entity $parent, RepositoryInterface $repository)
    {
        $this->parent = $parent; 
        $this->repository = $repository; 
        $this->query = $repository->getBuilder();
    }

    abstract public function get(); 

    /**
     * @param Entity[] $entities
     * @return array
     */
    abstract public function getEagerResults(array $entities): array;

    protected function getTable(): string
    {
        return $this->repository->getTable();
    }

    /**
     * @return class-string<Entity>
     */
    abstract protected function getRelatedEntity(): string;

    public function getQuery(): Builder
    {
        return $this->query;
    }

    public function getParent(): Entity
    {
        return $this->parent;
    }
}