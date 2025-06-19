<?php

namespace Framework\Database\ORM\Loaders;

use Framework\Database\ORM\Rel;

class LazyLoader extends AbstractLoader
{
    public function load(array $entities, array $relations): void
    {
        foreach ($entities as $entity) {
            foreach ($relations as $relationPath) {
                $this->loadNestedRelation($entity, $relationPath);
            }
        }
    }

    protected function loadNestedRelation($entity, string $relationPath): void
    {
        $segments = explode('.', $relationPath);
        $current = array_shift($segments);

        $relation = Rel::getRelation($entity, $current);

        if (!$relation) return;

        $results = $relation->getResults();
        $entity->setRelation($current, $results);

        // ⛓ 다음 단계가 있다면 재귀적으로 하위 엔티티에 적용
        if (!empty($segments)) {
            $nextPath = implode('.', $segments);

            $relatedEntities = is_array($results) ? $results : [$results];

            foreach ($relatedEntities as $relatedEntity) {
                if (!method_exists($relatedEntity, 'setRelation')) continue;

                $this->loadNestedRelation($relatedEntity, $nextPath);
            }
        }
    }
}
