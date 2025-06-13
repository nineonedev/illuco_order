<?php

namespace Framework\Database\ORM;

use Framework\Database\ORM\Entities\Entity;
use Framework\Database\ORM\Entities\MorphEntity;
use Framework\Database\ORM\Relations\{
    Relation,
    HasOne, HasMany, HasOneThrough, HasManyThrough,
    BelongsTo, BelongsToMany,
    MorphOne, MorphTo, MorphMany, MorphToMany, MorphedByMany
};
use Framework\Database\ORM\Relations\MorphRelation;
use Framework\Support\Collection;

/**
 * 관계 및 MorphMap을 담당하는 통합 Relation 헬퍼/레지스트리
 */
class RelationMap
{
    // morph type <-> class-string 맵
    protected static $morphs = [];

    // 관계형 config
    protected static $relations = [];

    protected static $configured = false; 

    /** === 관계 메타 데이터 등록/조회 === */

    public static function setRelationMap(array $relations): void
    {
        if (static::$configured) return;

        foreach ($relations as $class => $relationList) {
            
            if (!isset(static::$relations[$class])) {
                static::$relations[$class] = $relationList;
            } else {
                static::$relations[$class] = array_merge(
                    static::$relations[$class],
                    $relationList
                );
            }

            foreach ($relationList as $rel) {
                if (
                    isset($rel['type']) &&
                    is_subclass_of($rel['type'], MorphRelation::class)
                ) {
                    $related = $rel['args'][0] ?? null;

                    if (is_string($related) && is_subclass_of($related, Entity::class)) {
                        static::addMorph($related::alias(), $related);
                    }
                }
            }
        }

        static::$configured = true;
    }


    public static function reset(): void
    {
        static::$morphs = [];
        static::$relations = [];
        static::$permissionableClasses = [];
        static::$configured = false;
    }
    public static function config(): array
    {
        return static::$relations;
    }

    public static function getRelation(Entity $entity, string $relationName): ?Relation
    {
        $class = get_class($entity);
        $relations = static::relationsFor($class);

        if (!$relations) return null;

        // name이 일치하는 관계 찾기
        $relation = Collection::create($relations)
            ->find(fn($relation) => $relation['name'] === $relationName);

        if (!$relation) return null;

        return new $relation['type']($entity, ...$relation['args']);
    }


    public static function relationsFor($entityClass): array
    {
        return static::$relations[$entityClass] ?? [];
    }

    public static function morphMap(): array
    {
        return static::$morphs;
    }

    public static function resolveMorph(string $alias): ?string
    {
        return static::$morphs[$alias] ?? null;
    }

    public static function morphAlias(string $class): ?string
    {
        return array_search($class, static::$morphs, true) ?: null;
    }

    public static function addMorph(string $alias, string $class): void
    {
        if (isset(static::$morphs[$alias])) return; 
        
        static::$morphs[$alias] = $class;
    }

    protected static function getMorphTypeFromRelated(string $related): string
    {
        if (!is_subclass_of($related, MorphEntity::class)) {
            throw new \InvalidArgumentException(
                "Class [{$related}] must extend " . MorphEntity::class . " to be used in a morph relation."
            );
        }

        return $related::morphType();
    }


    /** === 관계 생성 DSL === */
    public static function hasOne(
        $name, 
        $related, 
        $foreignKey, 
        $localKey = 'id'
    )
    {
        return [
            'type' => HasOne::class, 
            'name' => $name, 
            'args' => [
                $related, 
                $foreignKey, 
                $localKey
            ]
        ];
    }

    public static function hasMany(
        $name, 
        $related, 
        $foreignKey, 
        $localKey = 'id'
    )
    {
        return ['type' => HasMany::class, 
            'name' => $name, 
            'args' => [
                $related, 
                $foreignKey, 
                $localKey
            ]
        ];
    }

    public static function belongsTo(
        $name, 
        $related, 
        $foreignKey, 
        $ownerKey = 'id'
    )
    {
        return [
            'type' => BelongsTo::class, 
            'name' => $name, 
            'args' => [
                $related, 
                $foreignKey, 
                $ownerKey
            ]
        ];
    }

    public static function belongsToMany(
        $name, 
        $related, 
        $pivotTable, 
        $foreignKey, 
        $relatedKey, 
        $relatedEntityPrimaryKey = 'id'
    )
    {
        return [
            'type' => BelongsToMany::class, 
            'name' => $name, 
            'args' => [
                $related, 
                $pivotTable, 
                $foreignKey, 
                $relatedKey, 
                $relatedEntityPrimaryKey
            ]
        ];
    }

    public static function hasOneThrough(
        $name, 
        $related, 
        $through, 
        $firstKey, 
        $secondKey, 
        $localKey = 'id')
    {
        return [
            'type' => HasOneThrough::class, 
            'name' => $name, 
            'args' => [
                $related, 
                $through, 
                $firstKey, 
                $secondKey, 
                $localKey
            ]
        ];
    }

    public static function hasManyThrough(
        $name, 
        $related, 
        $through, 
        $firstKey, 
        $secondKey, 
        $localKey = 'id'
    )
    {
        return [
            'type' => HasManyThrough::class, 
            'name' => $name, 
            'args' => [
                $related, 
                $through, 
                $firstKey, 
                $secondKey, 
                $localKey
            ]
        ];
    }

    // morph 계열
    public static function morphOne(
        $name, 
        $related, 
        $localKey = 'id', 
        $typeValue = null
    ) {
        $type = static::getMorphTypeFromRelated($related);

        return [
            'type' => MorphOne::class, 
            'name' => $name, 
            'args' => [
                $related, 
                "{$type}_type", 
                "{$type}_id", 
                $localKey, 
                $typeValue
            ]
        ];
    }


    public static function morphMany(
        $name, 
        $related, 
        $localKey = 'id', 
        $typeValue = null
    ) {
        $type = static::getMorphTypeFromRelated($related);

        return [
            'type' => MorphMany::class, 
            'name' => $name, 
            'args' => [
                $related, 
                "{$type}_type", 
                "{$type}_id", 
                $localKey, 
                $typeValue
            ]
        ];
    }

    public static function morphTo(
        string $type,
        array $typesMap = [],
        array $typeFieldMap = []
    ) {
        
        return [
            'type' => MorphTo::class,
            'name' => $type,
            'args' => [
                "{$type}_type",
                "{$type}_id",
                $typesMap,
                $typeFieldMap
            ]
        ];
    }


    public static function morphToMany(
        string $name,
        string $related,
        string $pivotTable,
        string $pivotRelatedKey,
        string $relatedEntityPrimaryKey = 'id',
        $typeValue = null
    ) {
        $type = static::getMorphTypeFromRelated($related);

        return [
            'type' => MorphToMany::class,
            'name' => $name,
            'args' => [
                $related,
                $pivotTable,
                $pivotRelatedKey,
                "{$type}_type",
                "{$type}_id",
                $relatedEntityPrimaryKey,
                $typeValue
            ]
        ];
    }


    public static function morphedByMany(
        string $name,
        string $related,
        string $pivotTable,
        string $pivotRelatedKey,
        string $relatedEntityPrimaryKey = 'id',
        $typeValue = null
    ) {
        $type = static::getMorphTypeFromRelated($related);

        return [
            'type' => MorphedByMany::class,
            'name' => $name,
            'args' => [
                $related,
                $pivotTable,
                $pivotRelatedKey,
                "{$type}_type",
                "{$type}_id",
                $relatedEntityPrimaryKey,
                $typeValue
            ]
        ];
    }


}
