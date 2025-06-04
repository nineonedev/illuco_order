<?php

namespace Framework\Database\Model;

use Framework\Database\Contracts\RepositoryInterface;
use Framework\Database\Model\Entities\Entity;
use Framework\Support\CallbackRegistry;
use Framework\Support\Contracts\ObserverInterface;
use Framework\Database\Model\Relations\Relation;
use Framework\Database\Model\Relations\{
    BelongsTo,
    BelongsToMany,
    HasOne,
    HasMany,
    HasOneThrough,
    HasManyThrough,
    MorphTo,
    MorphOne,
    MorphMany,
    MorphToMany,
    MorphedByMany
};

abstract class Model
{
    protected Entity $entity;
    protected ?RepositoryInterface $repository = null;
    protected CallbackRegistry $callbacks;
    protected array $relations = [];
    protected array $with = [];

    protected string $entityClass;
    protected string $repositoryClass;

    public function __construct(array $data = [])
    {
        $this->callbacks = new CallbackRegistry();
        $this->entity = $this->makeEntity($data);
        $this->repository = $this->makeRepository();
        $this->registerCallbacks();
    }

    protected function registerCallbacks(): void
    {
        foreach ($this->callbacks() as $event => $observers) {
            foreach ((array) $observers as $observer) {
                $this->callbacks->register(static::class, $event, $observer);
            }
        }
    }

    public function callbacks(): array
    {
        return [];
    }

    public function getTable(): string
    {
        return $this->getRepository()->getTable();
    }

    public function on(string $hook, ObserverInterface $observer): void
    {
        $this->callbacks->register(static::class, $hook, $observer);
    }

    public function getCallbackRegistry(): CallbackRegistry
    {
        return $this->callbacks;
    }

    public function getEntity(): Entity
    {
        return $this->entity;
    }

    public function makeEntity(array $data = []): Entity
    {
        $class = $this->entityClass;
        $entity = new $class($data);
        $entity->setMetadata('model_class', static::class);
        return $entity;
    }

    public function makeRepository(): RepositoryInterface
    {
        return app($this->repositoryClass, ['model' => $this]);
    }

    public function getRepository(): RepositoryInterface
    {
        return $this->repository ??= $this->makeRepository();
    }

    public function getPrimaryKey(): string
    {
        return $this->entity->getPrimaryKeyName();
    }

    public function set(string $key, $value): void
    {
        $this->entity->set($key, $value);
    }

    public function get(string $key)
    {
        return $this->entity->get($key);
    }

    public function toArray(): array
    {
        return $this->entity->getAttributes();
    }

    public function with(array $relations): self
    {
        $this->with = $relations;
        return $this;
    }

    public function getWith(): array
    {
        return $this->with;
    }

    public function relations(): array
    {
        return $this->relations;
    }

    protected function eagerLoad(array $entities): void
    {
        if (empty($this->with) || empty($entities)) {
            return;
        }

        $loader = new EagerLoader();
        $loader->loadFromModel($this, $entities);
    }

    public function save(): bool
    {
        $this->callbacks->run(ModelEvent::BEFORE_SAVE, [$this->entity]);
        $result = $this->getRepository()->save($this->entity);
        $this->callbacks->run(ModelEvent::AFTER_SAVE, [$this->entity]);
        return $result;
    }

    public function delete(): bool
    {
        $this->callbacks->run(ModelEvent::BEFORE_DELETE, [$this->entity]);
        $result = $this->getRepository()->delete($this->entity);
        $this->callbacks->run(ModelEvent::AFTER_DELETE, [$this->entity]);
        return $result;
    }

    public function update(array $attributes): bool
    {
        $this->callbacks->run(ModelEvent::BEFORE_UPDATE, [$this->entity]);
        $this->entity->fill($attributes);
        $result =  $this->save();
        $this->callbacks->run(ModelEvent::AFTER_UPDATE, [$this->entity]);
        return $result;
    }

    public function restore(): bool
    {
        $this->entity->restore();
        return $this->save();
    }

    // Static Factory API
    public static function create(array $data): self
    {
        $model = new static($data);
        $model->callbacks->run(ModelEvent::BEFORE_CREATE, [$model->entity]);
        $model->save();
        $model->callbacks->run(ModelEvent::AFTER_CREATE, [$model->entity]);
        return $model;
    }

    public static function find($id): ?self
    {
        $instance = new static();
        $entity = $instance->getRepository()->find($id);

        if (! $entity) return null;

        $model = new static($entity->getAttributes());
        $model->eagerLoad([$entity]);
        $model->callbacks->run(ModelEvent::AFTER_LOAD, [$entity]);

        return $model;
    }

    public static function all(): array
    {
        $instance = new static();
        $entities = $instance->getRepository()->all();

        $instance->eagerLoad($entities);
        $instance->callbacks->run(ModelEvent::AFTER_LOAD, $entities);

        return array_map(fn($e) => new static($e->getAttributes()), $entities);
    }

    public static function where(string $column, $value): array
    {
        $instance = new static();
        $entities = $instance->getRepository()->where($column, $value);

        $instance->eagerLoad($entities);
        $instance->callbacks->run(ModelEvent::AFTER_LOAD, $entities);

        return array_map(fn($e) => new static($e->getAttributes()), $entities);
    }

    public static function whereIn(string $column, array $values): array
    {
        $instance = new static();
        $entities = $instance->getRepository()->whereIn($column, $values);

        $instance->eagerLoad($entities);
        $instance->callbacks->run(ModelEvent::AFTER_LOAD, $entities);

        return array_map(fn($e) => new static($e->getAttributes()), $entities);
    }

    // Magic Access
    public function __get($key) { return $this->get($key); }
    public function __set($key, $value) { $this->set($key, $value); }

    // Relationship
    public function hasOne(
        string $relatedModelClass,
        string $foreignKey,
        string $localKey = 'id'
    ): HasOne
    {
        return new HasOne(
            $this,
            $relatedModelClass,
            $foreignKey,
            $localKey
        );
    }

    public function hasMany(
        string $relatedModelClass,
        string $foreignKey,
        string $localKey = 'id'
    ): HasMany
    {
        return new HasMany(
            $this,
            $relatedModelClass,
            $foreignKey,
            $localKey
        );
    }

    public function belongsTo(
        string $relatedModelClass,
        string $foreignKey,
        string $ownerKey = 'id'
    ): BelongsTo
    {
        return new BelongsTo(
            $this,
            $relatedModelClass,
            $foreignKey,
            $ownerKey
        );
    }

    public function hasOneThrough(
        string $relatedModelClass,
        string $throughModelClass,
        string $firstKey,
        string $secondKey,
        string $throughKey = 'id',
        string $localKey = 'id'
    ): HasOneThrough
    {
        return new HasOneThrough(
            $this,
            $relatedModelClass,
            $throughModelClass,
            $firstKey,
            $secondKey,
            $throughKey,
            $localKey
        );
    }

    public function hasManyThrough(
        string $relatedModelClass,
        string $throughModelClass,
        string $firstKey,
        string $secondKey,
        string $throughLocalKey = 'id',
        string $localKey = 'id'
    ): HasManyThrough
    {
        return new HasManyThrough(
            $this,
            $relatedModelClass,
            $throughModelClass,
            $firstKey,
            $secondKey,
            $throughLocalKey,
            $localKey
        );
    }

    public function belongsToMany(
        string $relatedModelClass,
        string $pivotTable,
        string $foreignPivotKey,
        string $relatedPivotKey,
        string $parentKey = 'id',
        string $relatedKey = 'id'
    ): BelongsToMany
    {
        return new BelongsToMany(
            $this,
            $relatedModelClass,
            $pivotTable,
            $foreignPivotKey,
            $relatedPivotKey,
            $parentKey,
            $relatedKey
        );
    }

    public function morphOne(
        string $relatedModelClass,
        string $morphName,
        string $localKey = 'id'
    ): MorphOne
    {
        return new MorphOne(
            $this,
            $relatedModelClass,
            $morphName,
            $localKey
        );
    }

    public function morphMany(
        string $relatedModelClass,
        string $morphName,
        string $localKey = 'id'
    ): MorphMany
    {
        return new MorphMany(
            $this,
            $relatedModelClass,
            $morphName,
            $localKey
        );
    }

    public function morphTo(
        string $morphName
    ): MorphTo
    {
        return new MorphTo(
            $this,
            $morphName
        );
    }

    public function morphToMany(
        string $relatedModelClass,
        string $morphName,
        string $pivotTable,
        string $localKey = 'id',
        string $relatedKey = 'id'
    ): MorphToMany
    {
        return new MorphToMany(
            $this,
            $relatedModelClass,
            $morphName,
            $pivotTable,
            $localKey,
            $relatedKey
        );
    }

    public function morphedByMany(
        string $relatedModelClass,
        string $morphName,
        string $pivotTable,
        string $localKey = 'id',
        string $relatedKey = 'id'
    ): MorphedByMany
    {
        return new MorphedByMany(
            $this,
            $relatedModelClass,
            $morphName,
            $pivotTable,
            $localKey,
            $relatedKey
        );
    }
}
