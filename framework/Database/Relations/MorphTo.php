<?php

namespace Framework\Database\Relations;

use Framework\Database\Contracts\RepositoryInterface;
use Framework\Database\Entities\Entity;

class MorphTo extends Relation
{
    protected string $morphName;
    protected string $typeColumn;
    protected string $idColumn;
    protected string $ownerKey;

    public function __construct(
        Entity $parent,
        string $morphName,
        string $ownerKey = 'id'
    ) {
        $this->morphName = $morphName;
        $this->typeColumn = $morphName . '_type';
        $this->idColumn = $morphName . '_id';
        $this->ownerKey = $ownerKey;

        // repository는 이 관계에선 의미 없음 → null
        parent::__construct($parent, new class implements RepositoryInterface {
            public function __call($name, $arguments) { throw new \LogicException('Not supported in MorphTo'); }
        });
    }

    public function get(): ?Entity
    {
        $type = $this->parent->{$this->typeColumn};
        $id = $this->parent->{$this->idColumn};

        if (!$type || !$id) return null;

        /** @var RepositoryInterface $repo */
        $repo = app()->make("repository:{$type}");

        return $repo->find($id);
    }

    public function getEagerResults(array $entities): array
    {
        $groups = [];

        foreach ($entities as $entity) {
            $type = $entity->{$this->typeColumn};
            $id = $entity->{$this->idColumn};

            if ($type && $id) {
                $groups[$type][] = $id;
            }
        }

        $results = [];

        foreach ($groups as $type => $ids) {
            /** @var RepositoryInterface $repo */
            $repo = app()->make("repository:{$type}");
            $related = $repo->getBuilder()->whereIn($this->ownerKey, $ids)->get();

            foreach ($related as $row) {
                $entity = $repo->createEntity((array)$row);
                $results[$type][$row->{$this->ownerKey}] = $entity;
            }
        }

        $final = [];

        foreach ($entities as $entity) {
            $type = $entity->{$this->typeColumn};
            $id = $entity->{$this->idColumn};
            $final[$entity->get('id')] = $results[$type][$id] ?? null;
        }

        return $final;
    }

    protected function getRelatedEntity(): string
    {
        return ''; // MorphTo는 동적이므로 의미 없음
    }
}
