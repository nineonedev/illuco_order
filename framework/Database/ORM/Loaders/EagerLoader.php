<?php

namespace Framework\Database\ORM\Loaders;

use Framework\Database\ORM\Rel;

class EagerLoader extends AbstractLoader
{
    public function load(array $entities, array $relations): void
    {
        if (empty($entities) || empty($relations)) return;

        foreach ($relations as $relationName) {
            $this->loadRelationForEntities($entities, $relationName);
        }
    }

    protected function loadRelationForEntities(array $entities, string $relationName): void
    {
        $entity = $this->getFirstEntity($entities);

        if (!$entity) return;

        $relation = Rel::getRelation($entity, $relationName);
        if (!$relation) return;

        $relation->addEagerConstraints($entities);
        $results = $relation->getEagerResults($entities);
        $relation->match($entities, $results, $relationName);
    }
}
