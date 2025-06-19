<?php

namespace Framework\Database\ORM\Loaders;

use Framework\Database\ORM\Rel;

class EagerLoader extends AbstractLoader
{
    public function load(array $entities, array $relations): void
    {
        if (empty($entities) || empty($relations)) return;

        foreach ($relations as $relationPath) {
            $this->loadNestedRelation($entities, explode('.', $relationPath));
        }
    }

    protected function loadNestedRelation(array $entities, array $segments): void
    {
        if (empty($segments)) return;

        $relationName = array_shift($segments);
        $entity = $this->getFirstEntity($entities);


        if (!$entity) return;

        $relation = Rel::getRelation($entity, $relationName);
        if (!$relation) return;

        // 부모 관계 로딩
        $relation->addEagerConstraints($entities);
        $results = $relation->getEagerResults($entities);
        $relation->match($entities, $results, $relationName);
        

        // 하위 관계 재귀 로딩
        if (!empty($segments)) {
            $relatedEntities = [];

            foreach ($entities as $entity) {
                $related = $entity->getRelation($relationName);

                if (is_array($related)) {
                    $relatedEntities = array_merge($relatedEntities, $related);
                } elseif ($related !== null) {
                    $relatedEntities[] = $related;
                }
            }

            if (!empty($relatedEntities)) {
                $this->loadNestedRelation($relatedEntities, $segments);
            }
        }
    }
}
