<?php

namespace Framework\Database\ORM\Entities;

use App\User\Entities\User;
use Framework\Database\ORM\Casts\CastFactory;
use Framework\Database\ORM\Repositories\Repository;

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

    protected array $meta = [];

    public function __construct(array $attributes = [])
    {
        $this->fill($attributes);
        $this->syncOriginal();
        $this->setup();
    }

    protected function setup(): void
    {

    }

    /**
     * @return static
     */
    public static function make(array $attributes)
    {
        return new static($attributes);
    }

    /**
     * @return class-string<Repository>
     */
    abstract public function repositoryClass(): string;

    public function setMeta(string $key, $value): void
    {
        $this->meta[$key] = $value;
    }

    public function createRepository(array $attributes): Repository
    {
        $repo = static::repositoryClass();
        return $repo::new($attributes);
    }

    public function getMeta(string $key, $default = null)
    {
        return $this->meta[$key] ?? $default;
    }

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
        
        // 3순위: meta
        if (isset($this->meta[$key])) {
            return $this->meta[$key];
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

    public function getAttributes(): array
    {
        return $this->attributes;
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

        if (!empty($this->meta)) {
            $arr = array_merge($arr, $this->meta);
        }
        
        return $arr;
    }
}
