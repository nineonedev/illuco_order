<?php

namespace Framework\Database\ORM;

use Framework\Database\ORM\Entities\Entity;
use Framework\Database\ORM\Entities\Morphable;
use Framework\Database\ORM\Relations\{
    Relation,
    HasOne, HasMany, HasOneThrough, HasManyThrough,
    BelongsTo, BelongsToMany,
    MorphOne, MorphTo, MorphMany, MorphToMany, MorphedByMany
};
use Framework\Support\Collection;

/**
 * 관계 및 MorphMap을 담당하는 통합 Relation 헬퍼/레지스트리
 */
class RelationMap
{
    // morph type <-> class-string 맵
    protected static $morphMap = [];

    // 관계형 config
    protected static $relationConfig = [];

    /** === 관계 메타 데이터 등록/조회 === */

    public static function setConfig(array $relations): void
    {
        foreach ($relations as $entityClass => $relation) {
            if (is_subclass_of($entityClass, Morphable::class)) {
                static::addMorph($entityClass::morphType(), $entityClass);
            }
        }
        static::$relationConfig = $relations;
    }

    public static function config(): array
    {
        return static::$relationConfig;
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

        // 올바른 생성: new ... (생성자에 파라미터 전달)
        return new $relation['type']($entity, ...$relation['args']);
    }


    public static function relationsFor($entityClass): array
    {
        return static::$relationConfig[$entityClass] ?? [];
    }

    /** === MorphMap 관련 === */

    public static function setMorphMap(array $map): void
    {
        static::$morphMap = $map;
    }

    public static function morphMap(): array
    {
        return static::$morphMap;
    }

    public static function resolveMorph(string $alias): ?string
    {
        return static::$morphMap[$alias] ?? null;
    }

    public static function morphAlias(string $class): ?string
    {
        return array_search($class, static::$morphMap, true) ?: null;
    }

    public static function addMorph(string $alias, string $class): void
    {
        static::$morphMap[$alias] = $class;
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
        $morphType = 'morph_type', 
        $morphId = 'morph_id', 
        $localKey = 'id', 
        $typeValue = null
    )
    {
        return [
            'type' => MorphOne::class, 
            'name' => $name, 
            'args' => [
                $related, 
                $morphType, 
                $morphId, 
                $localKey, 
                $typeValue
            ]
        ];
    }

    public static function morphMany(
        $name, 
        $related, 
        $morphType = 'morph_type', 
        $morphId = 'morph_id', 
        $localKey = 'id', 
        $typeValue = null
    )
    {
        return [
            'type' => MorphMany::class, 
            'name' => $name, 
            'args' => [
                $related, 
                $morphType, 
                $morphId, 
                $localKey, 
                $typeValue
            ]
        ];
    }

    public static function morphTo(
        $name, 
        $morphType = 'morph_type', 
        $morphId = 'morph_id', 
        $typesMap = [], 
        $typeFieldMap = []
    )
    {
        return ['type' => MorphTo::class, 
            'name' => $name, 
            'args' => [
                $morphType, 
                $morphId, 
                $typesMap, 
                $typeFieldMap
            ]
        ];
    }

    public static function morphToMany(
        $name, 
        $related, 
        $pivotTable, 
        $pivotRelatedKey, 
        $morphType = 'morph_type', 
        $morphId = 'morph_id', 
        $relatedEntityPrimaryKey = 'id', 
        $typeValue = null
    )
    {
        return ['type' => MorphToMany::class, 
            'name' => $name, 
            'args' => [
                $related, 
                $pivotTable, 
                $pivotRelatedKey, 
                $morphType, 
                $morphId, 
                $relatedEntityPrimaryKey, 
                $typeValue
            ]
        ];
    }

    public static function morphedByMany(
        $name, 
        $related, 
        $pivotTable, 
        $pivotRelatedKey, 
        $morphType = 'morph_type', 
        $morphId = 'morph_id',
        $relatedEntityPrimaryKey = 'id', 
        $typeValue = null
    )
    {
        return [
            'type' => MorphedByMany::class, 
            'name' => $name, 
            'args' => [
                $related, 
                $pivotTable, 
                $pivotRelatedKey, 
                $morphType, 
                $morphId, 
                $relatedEntityPrimaryKey, 
                $typeValue
            ]
        ];
    }

}
