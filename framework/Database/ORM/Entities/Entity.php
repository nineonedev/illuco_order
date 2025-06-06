<?php

namespace Framework\Database\ORM\Entities;

use App\User\Entities\User;
use Framework\Database\ORM\Casts\CastFactory;
use Framework\Database\ORM\Repositories\Repository;
use Framework\Database\ORM\Relations\{
    HasOne,
    HasMany,
    HasOneThrough,
    HasManyThrough,
    BelongsTo,
    BelongsToMany,
    MorphOne,
    MorphTo,
    MorphMany,
    MorphToMany,
    MorphedByMany
};
use PhpOffice\PhpSpreadsheet\Style\ConditionalFormatting\Wizard\Duplicates;

abstract class Entity
{
    protected string $primaryKey = 'id'; 

    /** @var array<string, mixed> 실제 속성 */
    protected array $attributes = [];

    /** @var array<string, mixed> 최초 데이터(변경감지용) */
    protected array $original = [];

    /** @var array<string> 허용 속성(없으면 전체 허용) */
    protected array $fillable = [];

    /** @var array<string> 금지 속성 */
    protected array $guarded = [];

    /** @var array<string, string> 캐스팅 지정(예: 'is_active' => 'bool') */
    protected array $casts = [];
    
    protected array $relations = [];

    public function __construct(array $attributes = [])
    {
        $this->fill($attributes);
        $this->syncOriginal();
    }

    /**
     * @return class-string<Repository>
     */
    abstract public function repositoryClass(): string;
    public function setRelation($name, $value): void
    {
        $this->relations[$name] = $value;
    }

    public function getRelation($name)
    {
        return $this->relations[$name] ?? null;
    }

    public function fill(array $attributes): self
    {
        $pk = $this->getPrimaryKeyName();

        foreach ($attributes as $key => $value) {
            if ($key === $pk || $this->isFillable($key)) {
                $this->__set($key, $value);
            }
        }

        return $this;
    }

    public function setPrimaryKey($value): void
    {
        $this->attributes[$this->getPrimaryKey()] = $value;
    }

    public function getPrimaryKeyName(): string
    {
        return $this->primaryKey;
    }

    public function getPrimaryKey()
    {
        return $this->__get($this->primaryKey);
    }

    // ---- 개선: 캐스트 팩토리 활용 ----
    protected function castAttribute(string $key, $value)
    {
        if (!isset($this->casts[$key])) {
            return $value;
        }

        $cast = CastFactory::resolve($this->casts[$key]);
        return $cast->get($value);
    }

    protected function castSet(string $key, $value)
    {
        if (!isset($this->casts[$key])) {
            return $value;
        }
        $cast = CastFactory::resolve($this->casts[$key]);
        return $cast->set($value);
    }

    // --- 매직 getter/setter ---
    public function __get($key)
    {
        // 1순위: relations
        if (isset($this->relations[$key])) {
            return $this->relations[$key];
        }
        // 2순위: attributes
        if (array_key_exists($key, $this->attributes)) {
            return $this->castAttribute($key, $this->attributes[$key]);
        }
        
        return null;
    }

    public function __set($key, $value): void
    {
        if ($key === $this->getPrimaryKeyName() || $this->isFillable($key)) {
            $this->attributes[$key] = $this->castSet($key, $value);
        }
    }

    public function __isset($key): bool
    {
        return isset($this->attributes[$key]);
    }

    public function __unset($key): void
    {
        unset($this->attributes[$key]);
    }

    public function set(string $key, $value): void
    {
        $this->__set($key, $value);
    }

    public function get(string $key, $default = null)
    {
        return $this->__get($key) ?? $default;
    }

    protected function isFillable(string $key): bool
    {
        if (!empty($this->fillable)) {
            return in_array($key, $this->fillable, true);
        }
        if (!empty($this->guarded)) {
            return !in_array($key, $this->guarded, true);
        }
        return true;
    }

    public function syncOriginal(): void
    {
        $this->original = $this->attributes;
    }

    public function getChanges(): array
    {
        $changes = [];
        foreach ($this->attributes as $key => $value) {
            if (!array_key_exists($key, $this->original) || $this->original[$key] !== $value) {
                $changes[$key] = $value;
            }
        }
        return $changes;
    }

    public function isDirty(): bool
    {
        return !empty($this->getChanges());
    }

    public function isClean(): bool
    {
        return empty($this->getChanges());
    }

    public function hasChanged(string $key): bool
    {
        return array_key_exists($key, $this->getChanges());
    }

    public function toArray(): array
    {
        
        $arr = [];
        foreach ($this->attributes as $key => $value) {
            $arr[$key] = $this->castAttribute($key, $value);
        }

        foreach ($this->relations as $relation => $data) {
            if (is_array($data) || $data instanceof \Traversable) {
                $arr[$relation] = array_map(
                    fn($item) => $item instanceof self ? $item->toArray() : $item,
                    is_array($data) ? $data : iterator_to_array($data)
                );
            } else if ($data instanceof self) {
                $arr[$relation] = $data->toArray();
            } else {
                $arr[$relation] = $data;
            }
        }
        
        return $arr;
    }


    // --- 1:1 관계 ---
    public function hasOne(
        string $relatedEntityClass,
        string $foreignKey,
        string $localKey = 'id'
    ): HasOne {
        return new HasOne(
            $this, 
            $relatedEntityClass, 
            $foreignKey, 
            $localKey
        );
    }

    // --- 1:N 관계 ---
    public function hasMany(
        string $relatedEntityClass,
        string $foreignKey,
        string $localKey = 'id'
    ): HasMany {
        return new HasMany(
            $this, 
            $relatedEntityClass, 
            $foreignKey, 
            $localKey
        );
    }

    // --- N:1 관계 ---
    public function belongsTo(
        string $relatedEntityClass,
        string $foreignKey,
        string $ownerKey = 'id'
    ): BelongsTo {
        return new BelongsTo(
            $this, 
            $relatedEntityClass, 
            $foreignKey, 
            $ownerKey
        );
    }

    // --- N:N(Pivot) 관계 ---
    public function belongsToMany(
        string $relatedEntityClass,
        string $pivotTable,
        string $foreignKey,
        string $relatedKey,
        string $relatedEntityPrimaryKey = 'id'
    ): BelongsToMany {
        return new BelongsToMany(
            $this, 
            $relatedEntityClass, 
            $pivotTable, 
            $foreignKey, 
            $relatedKey, 
            $relatedEntityPrimaryKey
        );
    }

    // --- MorphTo(다형성 역방향) ---
    public function morphTo(
        string $morphType,
        string $morphId,
        array $typesMap,
        array $typeFieldMap = []
    ): MorphTo {
        return new MorphTo(
            $this, 
            $morphType, 
            $morphId, 
            $typesMap, 
            $typeFieldMap
        );
    }

    // --- MorphMany(다형성 1:N 정방향) ---
    public function morphMany(
        string $relatedEntityClass,
        string $morphType,
        string $morphId,
        string $localKey = 'id',
        string $typeValue = null
    ): MorphMany {
        return new MorphMany(
            $this, 
            $relatedEntityClass, 
            $morphType, 
            $morphId, 
            $localKey, 
            $typeValue
        );
    }

    // --- MorphOne(다형성 1:1 정방향) ---
    public function morphOne(
        string $relatedEntityClass,
        string $morphType,
        string $morphId,
        string $localKey = 'id',
        string $typeValue = null
    ): MorphOne {
        return new MorphOne(
            $this, 
            $relatedEntityClass, 
            $morphType, 
            $morphId, 
            $localKey, 
            $typeValue
        );
    }

    // --- MorphedByMany(다형성 N:N 역방향) ---
    public function morphedByMany(
        string $relatedEntityClass,
        string $pivotTable,
        string $morphType,
        string $morphId,
        string $pivotRelatedKey,
        string $relatedEntityPrimaryKey = 'id',
        string $typeValue = null
    ): MorphedByMany {
        return new MorphedByMany(
            $this, 
            $relatedEntityClass, 
            $pivotTable, 
            $morphType, 
            $morphId, 
            $pivotRelatedKey, 
            $relatedEntityPrimaryKey, 
            $typeValue
        );
    }

    // --- MorphToMany(다형성 N:N 정방향) ---
    public function morphToMany(
        string $relatedEntityClass,
        string $pivotTable,
        string $morphType,
        string $morphId,
        string $pivotRelatedKey,
        string $relatedEntityPrimaryKey = 'id',
        string $typeValue = null
    ): MorphToMany {
        return new MorphToMany(
            $this, 
            $relatedEntityClass, 
            $pivotTable, 
            $morphType, 
            $morphId, 
            $pivotRelatedKey, 
            $relatedEntityPrimaryKey, 
            $typeValue
        );
    }

    // --- HasOneThrough ---
    public function hasOneThrough(
        string $relatedEntityClass,
        string $throughEntityClass,
        string $firstKey,
        string $secondKey,
        string $localKey = 'id'
    ): HasOneThrough {
        return new HasOneThrough(
            $this, 
            $relatedEntityClass, 
            $throughEntityClass, 
            $firstKey, 
            $secondKey, 
            $localKey
        );
    }

    // --- HasManyThrough ---
    public function hasManyThrough(
        string $relatedEntityClass,
        string $throughEntityClass,
        string $firstKey,
        string $secondKey,
        string $localKey = 'id'
    ): HasManyThrough {
        return new HasManyThrough(
            $this, 
            $relatedEntityClass, 
            $throughEntityClass, 
            $firstKey, 
            $secondKey, 
            $localKey
        );
    }
}
