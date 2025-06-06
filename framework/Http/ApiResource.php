<?php

namespace Framework\Http\Resources;

use Framework\Database\Model\Entities\Entity;

abstract class ApiResource
{
    protected Entity $entity;

    public function __construct($entity)
    {
        $this->entity = $entity;
    }

    abstract public function toArray(): array;

    public static function from(Entity $entity): array
    {
        return (new static($entity))->toArray();
    }

    /**
     * @param iterable<Entity> $items
     */
    public static function collection(iterable $items): array

    {
        $result = [];

        foreach ($items as $item) {
            $result[] = static::from($item);
        }

        return $result;
    }
}
