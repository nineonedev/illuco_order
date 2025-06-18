<?php

namespace Framework\Database\ORM\Entities;

use Framework\Database\ORM\Casts\CastFactory;
use Framework\Database\ORM\Rel;
use Framework\Database\ORM\Relations\Relation;
use Framework\Database\ORM\Repositories\Repository;
use Framework\Database\ORM\Traits\SoftDeletes;
use Framework\Database\Paginator\Paginator;

abstract class Entity
{
    protected string $primaryKey = 'id';

    protected array $attributes = [];

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
        $this->setup();
        $this->fill($attributes);
        $this->syncOriginal();
        $this->boot();
    }

    protected function setup(): void {}
    protected function boot(): void {

        if (trait_used(SoftDeletes::class, $this)) {
            $this->fillable = array_merge($this->fillable, [$this->getSoftDeleteColumn()]);
        }
    }

    public static function make(array $attributes = [])
    {
        return new static($attributes);
    }
    
    /** @return class-string<Repository> */
    abstract public static function repositoryClass(): string;

    public static function resolveRepository(): Repository
    {
        return static::repositoryClass()::make();
    }

    public static function alias(): string
    {
        return strtolower(class_basename(static::class));
    }

    public function relation(string $name): ?Relation
    {
        return $this->getMeta($name . '_relation');
    }

    public function setMeta(string $key, $value): void
    {
        $this->meta[$key] = $value;
    }

    public function getMeta(string $key, $default = null)
    {
        return $this->meta[$key] ?? $default;
    }

    public function setRelation(string $name, $value): void
    {
        $this->relations[$name] = $value;
    }

    public function getRelation(string $name)
    {
        return $this->relations[$name] ?? null;
    }

    public function hasRelation(string $name): bool
    {
        return array_key_exists($name, $this->relations);
    }

    public function rowNumber(): ?int
    {
        return $this->getMeta(Paginator::ROW_NUMBER_KEY);
    }

    /**
     * @return static
     */
    public function fill(array $attributes)
    {
        $pk = $this->getPrimaryKeyName();

        foreach ($attributes as $key => $value) {
            if ($key === $pk || $this->isFillable($key)) {
                $this->__set($key, $value);
            }
        }

        return $this;
    }

    public function pivot(string $key = null)
    {
        $pivot = $this->getMeta('pivot') ?? [];

        if ($key) return $pivot[$key] ?? null;

        return $pivot;
    }

    public function setPrimaryKey($value): void
    {
        $this->attributes[$this->primaryKey] = $value;
    }

    public function getPrimaryKeyName(): string
    {
        return $this->primaryKey;
    }

    public function getPrimaryKey()
    {
        return $this->__get($this->primaryKey);
    }

    protected function castAttribute(string $key, $value)
    {
        if (!isset($this->casts[$key])) return $value;
        return CastFactory::resolve($this->casts[$key])->get($value);
    }

    protected function castSet(string $key, $value)
    {
        if (!isset($this->casts[$key])) return $value;
        return CastFactory::resolve($this->casts[$key])->set($value);
    }

    public function __get(string $key)
    {
        if (array_key_exists($key, $this->attributes)) {
            return $this->castAttribute($key, $this->attributes[$key]);
        }

        if (array_key_exists($key, $this->relations)) {
            return $this->relations[$key];
        }

        if (array_key_exists($key, $this->meta)) {
            return $this->meta[$key];
        }

        return $this->meta[$key] ?? null;
    }

    public function forgetRelation(string $name): void
    {
        unset($this->relations[$name]);
    }

    public function __set(string $key, $value): void
    {
        if ($key === $this->primaryKey || $this->isFillable($key)) {
            $this->attributes[$key] = $this->castSet($key, $value);
        }
    }

    public function __isset(string $key): bool
    {
        return isset($this->attributes[$key]);
    }

    public function __unset(string $key): void
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
        return !empty($this->fillable) ? in_array($key, $this->fillable, true) :
                (!empty($this->guarded) ? !in_array($key, $this->guarded, true) : true);
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

    public function getAttributes(): array
    {
        return $this->attributes;
    }

    public function toArray(): array
    {
        $arr = [];

        foreach ($this->attributes as $key => $value) {
            $arr[$key] = $this->castAttribute($key, $value);
        }

        foreach ($this->relations as $relation => $data) {
            if (is_array($data)) {
                $arr[$relation] = [];

                foreach ($data as $item) {
                    $arr[$relation][] = $item instanceof self ? $item->toArray() : $item;
                }

            } elseif ($data instanceof \Traversable) {
                $arr[$relation] = [];

                foreach ($data as $item) {
                    $arr[$relation][] = $item instanceof self ? $item->toArray() : $item;
                }

            } elseif ($data instanceof self) {
                $arr[$relation] = $data->toArray();
            } else {
                $arr[$relation] = $data;
            }
        }

        return array_merge($arr, $this->meta);
    }

    public function __call(string $method, array $args)
    {
        $relation = Rel::getRelation($this, $method);

        if ($relation) {
            return $relation;
        }

        throw new \BadMethodCallException("Method {$method} does not exist.");
    }
}
