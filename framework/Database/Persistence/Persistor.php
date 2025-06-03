<?php

namespace Framework\Database\Persistence;

use Framework\Database\Entities\Entity;

class Persistor
{
    public static function save(Entity $entity): bool
    {
        return $entity->repository()->save($entity);
    }

    public static function delete(Entity $entity): bool
    {
        return $entity->repository()->delete($entity);
    }
}
