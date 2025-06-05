<?php

namespace Framework\Database\Model;

use Framework\Database\Model\Entities\Entity;
use Framework\Database\Model\Relations\Relation;
use InvalidArgumentException;
use ReflectionMethod;

class EagerLoader
{
    /**
     * 지정된 관계들을 Eager Load 처리
     *
     * @param Entity[] $entities
     * @param string[]         $relations
     * @return void
     */
    public function load(array $entities, array $relations): void
    {
        if (empty($entities) || empty($relations)) {
            return;
        }

        $entityClass = get_class($entities[0]);
        $entity = new $entityClass();
        $relationMap = $this->getRelationMap($entity);

        foreach ($relations as $relationName) {
            if (!isset($relationMap[$relationName])) {
                continue;
            }

            /** @var Relation $relation */
            $relation = call_user_func($relationMap[$relationName], $entities[0]);

            $relation->addEagerConstraints($entities);
            $results = $relation->getEagerResults($entities);

            // 👇 핵심 수정: 각 엔티티에 직접 setRelation()
            foreach ($entities as $e) {
                $related = $results[$e->getPrimaryKey()] ?? null;
                $e->setRelation($relationName, $related);
            }
        }
    }


    /**
     * 리플렉션을 사용해 Relation 메서드 자동 매핑
     *
     * @param Entity $entity
     * @return array<string, callable>
     */
    protected function getRelationMap(Entity $entity): array
    {
        $map = [];
        $ref = new \ReflectionClass($entity);

        foreach ($ref->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            if ($method->getNumberOfParameters() === 0) {
                $result = $method->invoke($entity);

                if ($result instanceof Relation) {
                    $map[$method->getName()] = function (Entity $context) use ($method) {
                        return $method->invoke($context);
                    };
                }
            }
        }

        return $map;
    }
}
