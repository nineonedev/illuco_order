<?php

namespace Framework\Database\Relations;

use Framework\Database\Entities\Entity;
use Framework\Database\Repositories\NullRepository;
use Framework\Database\Repositories\RepositoryResolver;

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
        $this->typeColumn = "{$morphName}_type";
        $this->idColumn = "{$morphName}_id";
        $this->ownerKey = $ownerKey;

        parent::__construct($parent, new NullRepository());
    }

    public function get(): ?Entity
    {
        $type = $this->parent->get($this->typeColumn);
        $id = $this->parent->get($this->idColumn);

        if (!$type || !$id) {
            return null;
        }

        $repo = RepositoryResolver::resolveFromEntity($type);
        return $repo->find($id);
    }

    public function getEagerResults(array $entities): array
    {
        $groups = [];

        foreach ($entities as $entity) {
            $type = $entity->get($this->typeColumn);
            $id = $entity->get($this->idColumn);

            if ($type && $id) {
                $groups[$type][] = $id;
            }
        }

        $results = [];

        foreach ($groups as $type => $ids) {
            $repo = RepositoryResolver::resolveFromEntity($type);
            $records = $repo->getBuilder()->whereIn($this->ownerKey, $ids)->get();

            foreach ($records as $record) {
                $entity = $repo->createEntity((array)$record);
                $results[$type][$record->{$this->ownerKey}] = $entity;
            }
        }

        $final = [];

        foreach ($entities as $entity) {
            $type = $entity->get($this->typeColumn);
            $id = $entity->get($this->idColumn);
            $final[$entity->get('id')] = $results[$type][$id] ?? null;
        }

        return $final;
    }

    protected function getRelatedEntity(): string
    {
        return ''; // MorphTo는 동적이므로 명시적 클래스 없음
    }
}
