<?php

namespace Framework\Database\Model;

use Framework\Database\Model\Entities\Entity;
use Framework\Database\Model\Relations\Relation;

class EagerLoader
{
    /**
     * 지정된 관계들을 Eager Load 처리
     *
     * @param Entity[]  $entities
     * @param string[]  $relations
     * @return void
     */
    public function load(array $entities, array $relations): void
    {
        if (empty($entities) || empty($relations)) {
            return;
        }

        $entityClass = get_class($entities[0]);
        $instance = new $entityClass();
        $relationMap = $instance->relations();

        foreach ($relations as $relationName) {
            if (!isset($relationMap[$relationName])) {
                continue;
            }

            /** @var Relation $relation */
            $relation = call_user_func($relationMap[$relationName], $entities[0]);

            $relation->addEagerConstraints($entities);
            $results = $relation->getEagerResults($entities);
            $relation->match($entities, $results, $relationName);
        }
    }
}

