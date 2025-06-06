<?php

namespace Framework\Database\ORM;

use Framework\Database\ORM\Entities\Entity;
use Framework\Database\ORM\Relations\Relation;

class ORM
{
    /** @var array<class-string<Entity>, array<string, callable>> 엔티티별 관계정보 */
    protected static array $entityRelations = [];

    /** @var array<string, array<string, mixed>> 관계형 캐시 */
    protected static array $relationCache = [];

    /** @var array<string, array<string, bool>> 순환관계 체크 캐시 */
    protected static array $cycleCheck = [];
    
    /**
     * 관계 자동 등록 (Reflection 활용)
     */
    public static function discoverRelations(Entity $entity): void
    {
        $class = get_class($entity);
        if (isset(static::$entityRelations[$class])) {
            return;
        }

        $relations = [];
        $ref = new \ReflectionClass($entity);

        foreach ($ref->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
            if ($method->getNumberOfParameters() > 0 || $method->isStatic()) continue;
            $name = $method->getName();
            $relations[$name] = function($entity) use ($method) {
                return $method->invoke($entity);
            };
        }

        static::$entityRelations[$class] = $relations;
    }

    /**
     * 해당 엔티티의 모든 관계명/클로저 반환
     */
    public static function getRelations(Entity $entity): array
    {
        $class = get_class($entity);
        static::discoverRelations($entity);
        return static::$entityRelations[$class] ?? [];
    }

    /**
     * 특정 관계명에 해당하는 관계 객체 반환
     */
    public static function getRelation(Entity $entity, string $relationName): ?Relation
    {
        $relations = static::getRelations($entity);
        if (isset($relations[$relationName])) {
            return $relations[$relationName]($entity);
        }
        return null;
    }

    // --- 관계형 캐시 관리 ---
    public static function getRelationCache(string $entityClass, string $relationName)
    {
        return static::$relationCache[$entityClass][$relationName] ?? null;
    }

    public static function putRelationCache(string $entityClass, string $relationName, $data): void
    {
        static::$relationCache[$entityClass][$relationName] = $data;
    }

    public static function clearRelationCache(string $entityClass = null): void
    {
        if ($entityClass) {
            unset(static::$relationCache[$entityClass]);
        } else {
            static::$relationCache = [];
        }
    }

    // --- 순환관계 체크 ---
    public static function checkCycle(Entity $entity, string $relationName): bool
    {
        $class = get_class($entity);
        if (isset(static::$cycleCheck[$class][$relationName])) {
            return static::$cycleCheck[$class][$relationName];
        }
        $relation = static::getRelation($entity, $relationName);
        if (!$relation) return false;
        $relatedClass = method_exists($relation, 'getRelatedEntityClass')
            ? $relation->getRelatedEntityClass()
            : null;
        static::$cycleCheck[$class][$relationName] = ($class === $relatedClass);
        return static::$cycleCheck[$class][$relationName];
    }
}
