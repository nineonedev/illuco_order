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
        $withRelations = $this->groupNestedRelations($model->getWith());

        foreach ($withRelations as $relationName => $nested) {
            if (!isset($relationMap[$relationName])) {
                continue;
            }

            /** @var Relation $relation */
            $relation = call_user_func($relationMap[$relationName], $model);

            $relation->addEagerConstraints($entities);
            $results = $relation->getEagerResults($entities);
            $relation->match($entities, $results, $relationName);

            // 중첩된 관계 재귀 처리
            if (!empty($nested)) {
                $relatedModels = [];

                foreach ($entities as $parent) {
                    $related = $parent->getRelation($relationName);

                    if (is_array($related)) {
                        $relatedModels = array_merge($relatedModels, $related);
                    } elseif ($related !== null) {
                        $relatedModels[] = $related;
                    }
                }


                if (!empty($relatedModels)) {
                    $relatedModelClass = $relation->getRelatedModel()::class;
                    $relatedModel = new $relatedModelClass();
                    $relatedModel->with($nested);

                    $this->loadFromModel($relatedModel, $relatedModels);
                }
            }
        }
    }

    protected function groupNestedRelations(array $with): array
    {
        $grouped = [];

        foreach ($with as $relation) {
            if (strpos($relation, '.') === false) {
                $grouped[$relation] = [];
            } else {
                [$top, $rest] = explode('.', $relation, 2);
                $grouped[$top][] = $rest;
            }
        }

        return $grouped;
    }
}
