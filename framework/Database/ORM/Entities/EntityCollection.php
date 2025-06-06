<?php

namespace Framework\Database\ORM\Entities;

use Framework\Support\Collection;

class EntityCollection extends Collection
{
    public function toArray(): array
    {
        return $this->map(fn($entity) => $entity instanceof Entity ? $entity->toArray() : $entity)->all();
    }
}