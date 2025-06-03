<?php

namespace Framework\Database\Model\Relations;

use Framework\Database\Model\Entities\Entity;
use Framework\Database\Contracts\RepositoryInterface;
use Framework\Support\Str;

class MorphTo extends Relation
{
    protected string $typeColumn;
    protected string $idColumn;
    protected array $eagerMap = [];

    public function __construct(
        Entity $parent,
        string $typeColumn = 'morphable_type',
        string $idColumn = 'morphable_id'
    ) {
        // repository는 사용 안 함 → dummy
        parent::__construct($parent, new class implements RepositoryInterface {});
        $this->typeColumn = $typeColumn;
        $this->idColumn = $idColumn;
    }

    public function getResults(): ?Entity
    {
        $type = $this->parent->get($this->typeColumn);
        $id = $this->parent->get($this->idColumn);

        if (!$type || !$id) {
            return null;
        }

        /** @var RepositoryInterface $repo */
        $repo = app($type::repositoryClass());

        return $repo->find($id);
    }

    public function initRelation(array $entities, string $relation): array
    {
        foreach ($entities as $entity) {
            $entity->set($relation, null);
        }

        return $entities;
    }

    public function addEagerConstraints(array $entities): void
    {
        foreach ($entities as $entity) {
            $type = $entity->get($this->typeColumn);
            $id = $entity->get($this->idColumn);

            if ($type && $id) {
                $this->eagerMap[$type][] = $id;
            }
        }
    }

    public function getEagerResults(array $entities): array
    {
        $results = [];

        foreach ($this->eagerMap as $type => $ids) {
            /** @var RepositoryInterface $repo */
            $repo = app($type::repositoryClass());

            foreach ($repo->whereIn($repo->getPrimaryKey(), array_unique($ids)) as $item) {
                $results[$type][$item->get($repo->getPrimaryKey())] = $item;
            }
        }

        return $results;
    }

    public function match(array $entities, array $results, string $relation): array
    {
        foreach ($entities as $entity) {
            $type = $entity->get($this->typeColumn);
            $id = $entity->get($this->idColumn);

            if (isset($results[$type][$id])) {
                $entity->set($relation, $results[$type][$id]);
            }
        }

        return $entities;
    }

    protected function getKeys(array $entities): array
    {
        return []; // 불필요 in MorphTo
    }
}
