<?php

namespace Framework\Database\Model;

use Framework\Database\Model\Entities\Entity;
use Framework\Database\Model\Relations\Relation;
use ReflectionClass;
use ReflectionMethod;

class RelationResolver
{
    /**
     * @param Entity $entity
     * @return array<string, Relation>
     */
    public static function resolveAll(Entity $entity): array
    {
        $relations = [];
        $ref = new ReflectionClass($entity);

        foreach ($ref->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            if ($method->getNumberOfParameters() > 0) {
                continue;
            }

            $return = $method->invoke($entity);

            if ($return instanceof Relation) {
                $relations[$method->getName()] = $return;
            }
        }

        return $relations;
    }

    /**
     * @param Entity $entity
     * @param string $name
     * @return Relation|null
     */
    public static function resolveOne(Entity $entity, string $name): ?Relation
    {
        if (!method_exists($entity, $name)) {
            return null;
        }

        $result = $entity->$name();

        return $result instanceof Relation ? $result : null;
    }
}
