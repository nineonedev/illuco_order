<?php

namespace Framework\Database\Relations;

use Framework\Database\Entities\Entity;

/**
 * 관계를 한 번에 일괄 로딩하는 클래스
 */
class EagerLoader
{
    /**
     * @param Entity[] $entities
     * @param array $relations
     */
    public function load(array $entities, array $relations): void
    {
        if (empty($entities)) return;

        $first = $entities[0];

        foreach ($relations as $relationName => $nested) {
            if (is_int($relationName)) {
                $relationName = $nested;
                $nested = [];
            }

            $relation = $first->{$relationName}();
            $results = $relation->getEagerResults($entities);

            foreach ($entities as $entity) {
                $id = $entity->getPrimaryKey();
                $related = $results[$id] ?? (str_ends_with(get_class($relation), 'One') ? null : []);
                $entity->setRelation($relationName, $related);
            }

            if (!empty($nested)) {
                $relatedEntities = [];

                foreach ($entities as $entity) {
                    $rel = $entity->getRelation($relationName);
                    if (is_array($rel)) {
                        $relatedEntities = array_merge($relatedEntities, $rel);
                    } elseif ($rel instanceof Entity) {
                        $relatedEntities[] = $rel;
                    }
                }

                $this->load($relatedEntities, $nested);
            }
        }
    }

}
