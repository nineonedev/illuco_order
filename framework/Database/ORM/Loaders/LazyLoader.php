<?php

namespace Framework\Database\ORM\Loaders;

use Framework\Database\ORM\Entities\Entity;
use Framework\Database\ORM\ORM;

class LazyLoader extends AbstractLoader
{
    public function load(array $entities, array $relations): void
    {
        foreach ($entities as $entity) {
            foreach ($relations as $relationName) {
                $relation = ORM::getRelation($entity, $relationName);

                if (!$relation) continue;

                $entity->setRelation($relationName, $relation->getResults());
            }
        }
    }
}
