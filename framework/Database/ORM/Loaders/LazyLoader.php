<?php

namespace Framework\Database\ORM\Loaders;

use Framework\Database\ORM\Rel;

class LazyLoader extends AbstractLoader
{
    public function load(array $entities, array $relations): void
    {
        foreach ($entities as $entity) {
            foreach ($relations as $relationName) {
                $relation = Rel::getRelation($entity, $relationName);

                if (!$relation) continue;

                $entity->setRelation($relationName, $relation->getResults());
            }
        }
    }
}
