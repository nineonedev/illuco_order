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
use Framework\Support\Collection;
use RuntimeException;

class Rel
{
    /** @var array<class-string<MorphEntity>,class-string[]> */
    protected static $morphMap = [];

    /** @var array<string,class-string<Entity>> */
    protected static $relationMap = [];

    /** @var array<string,class-string<MorphEntity>> */
    protected static $morphableMap = [];

    protected static $configured = false;

    public static function setConfig(array $relationMap): void
    {
        if (static::$configured) return; 
        static::$relationMap = $relationMap;
        static::$configured = true; 
    }

    public static function reset(): void
    {
        static::$morphMap = [];
        static::$relationMap = [];
        static::$configured = false;
    }

    public static function getConfig(): array
    {
        return static::$relationMap;
    }

    public static function getRelation(Entity $entity, string $relationName): ?Relation
    {
        $class = get_class($entity);
        $relationMap = static::relationsFor($class);

        if (!$relationMap) return null;

        $relation = Collection::create($relationMap)
            ->find(fn($rel) => $rel['name'] === $relationName);

        if (!$relation) return null;
        return new $relation['type']($entity, ...$relation['args']);
    }

    public static function relationsFor($entityClass): array
    {
        return static::$relationMap[$entityClass] ?? [];
    }

    public static function morphableMap(): array
    {
        return static::$morphableMap;
    }

    public static function morphMap(): array
    {
        return static::$morphMap;
    }

    public static function setMorph(array $morphMap)
    {
        $map = []; 

        foreach ($morphMap as $morphClass => $morphables) {

            if (!is_subclass_of($morphClass, MorphEntity::class)) {
                throw new RuntimeException("MorphClass muset be extend MorphEntity.");
            }

            $row = []; 
            
            foreach ($morphables as $morphableClass) {
                $row[][$morphableClass::alias()] = $morphableClass;
                
                if (!isset(static::$morphableMap[$morphableClass::alias()])) {
                    static::$morphableMap[$morphableClass::alias()] = $morphableClass;
                }
            }

            $map[$morphClass] = $row;
        }

        static::$morphMap = $morphMap;
    }

    // === 관계 생성 DSL ===

    public static function hasOne(
        string $name, string $related, string $foreignKey, string $localKey = 'id'
    ): array {
        return [
            'type' => HasOne::class,
            'name' => $name,
            'args' => [$related, $foreignKey, $localKey]
        ];
    }

    public static function hasMany(
        string $name, string $related, string $foreignKey, string $localKey = 'id'
    ): array {
        return [
            'type' => HasMany::class,
            'name' => $name,
            'args' => [$related, $foreignKey, $localKey]
        ];
    }

    public static function belongsTo(
        string $name, string $related, string $foreignKey, string $ownerKey = 'id'
    ): array {
        return [
            'type' => BelongsTo::class,
            'name' => $name,
            'args' => [$related, $foreignKey, $ownerKey]
        ];
    }

    public static function belongsToMany(
        string $name, string $related, string $pivotTable, string $foreignKey, string $relatedKey, string $relatedEntityPrimaryKey = 'id'
    ): array {
        return [
            'type' => BelongsToMany::class,
            'name' => $name,
            'args' => [$related, $pivotTable, $foreignKey, $relatedKey, $relatedEntityPrimaryKey]
        ];
    }

    public static function hasOneThrough(
        string $name, string $related, string $through, string $firstKey, string $secondKey, string $localKey = 'id'
    ): array {
        return [
            'type' => HasOneThrough::class,
            'name' => $name,
            'args' => [$related, $through, $firstKey, $secondKey, $localKey]
        ];
    }

    public static function hasManyThrough(
        string $name, string $related, string $through, string $firstKey, string $secondKey, string $localKey = 'id'
    ): array {
        return [
            'type' => HasManyThrough::class,
            'name' => $name,
            'args' => [$related, $through, $firstKey, $secondKey, $localKey]
        ];
    }

    protected static function checkMorphEntity(string $related): void
    {
        if (!is_subclass_of($related, MorphEntity::class)) {
            throw new RuntimeException(
                "Class [{$related}] must extend " . MorphEntity::class . " to be used in a morph relation."
            );
        }
    }

    public static function getMorphByAlias(string $morphableClass): ?string
    {
        foreach (static::$morphMap as $morphClass => $morphables) {
            if (in_array($morphableClass, $morphables)) {
                return $morphClass;
            }
        }

        return null;
    }

    // === Morph 계열 자동화 ===

    public static function morphOne(
        string $related,
        string $localKey = 'id'
    ): array {
        static::checkMorphEntity($related);

        return [
            'type' => MorphOne::class,
            'name' => $related::alias(),
            'args' => [
                $related,
                $related::getMorphType(),
                $related::getMorphId(),
                $localKey,
            ]
        ];
    }

    public static function morphMany(
        string $related, 
        string $localKey = 'id'
    ): array {
        static::checkMorphEntity($related);

        return [
            'type' => MorphMany::class,
            'name' => $related::alias(),
            'args' => [
                $related,
                $related::getMorphType(),
                $related::getMorphId(),
                $localKey,
            ]
        ];
    }

    /**
     * @param array<class-string<Entity>> $morphables
     */
    public static function morphTo(
        string $related,
        array $morphables = []
    ): array {
        static::setMorph([$related => $morphables]);

        return [
            'type' => MorphTo::class,
            'name' => $related::morphType(),
            'args' => [
                $related::getMorphType(),
                $related::getMorphId(),
            ]
        ];
    }

    public static function morphToMany(
        string $related, 
        string $pivotTable, 
        string $pivotRelatedKey, 
        string $relatedEntityPrimaryKey = 'id'
    ): array {
        
        return [
            'type' => MorphToMany::class,
            'name' => $related::alias(),
            'args' => [
                $related,
                $pivotTable,
                $pivotRelatedKey,
                $related::getMorphType(),
                $related::getMorphId(),
                $relatedEntityPrimaryKey
            ]
        ];
    }

    public static function morphedByMany(
        string $name, string $related, string $pivotTable, string $pivotRelatedKey, string $relatedEntityPrimaryKey = 'id', $typeValue = null
    ): array {

        return [
            'type' => MorphedByMany::class,
            'name' => $name,
            'args' => [
                $related,
                $pivotTable,
                $pivotRelatedKey,
                $related::getMorphType(),
                $related::getMorphId(),
                $relatedEntityPrimaryKey,
                $typeValue ?? $related::alias()
            ]
        ];
    }
}
