<?php

namespace Framework\Database\Model;

use Framework\Database\Model\Relations\Relation;

class EagerLoader
{
    /**
     * 지정된 모델에 대해 Eager Load 처리
     *
     * @param Model       $model
     * @param object[]    $entities  (Entity[])
     * @return void
     */
    public function loadFromModel(Model $model, array $entities): void
    {
        if (empty($entities)) {
            return;
        }

        $relationMap = $model->relations();
        $withRelations = $model->getWith();

        foreach ($withRelations as $relationName) {
            if (!isset($relationMap[$relationName])) {
                continue;
            }

            /** @var Relation $relation */
            $relation = call_user_func($relationMap[$relationName], $model);

            $relation->addEagerConstraints($entities);
            $results = $relation->getEagerResults($entities);
            $relation->match($entities, $results, $relationName);
        }
    }
}
