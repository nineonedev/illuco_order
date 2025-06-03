<?php

namespace Framework\Database\Repositories;

use Framework\Database\Contracts\RepositoryInterface;
use InvalidArgumentException;

class RepositoryResolver
{
    public static function resolveFromEntity(string $entityClass): RepositoryInterface
    {
        if (!class_exists($entityClass)) {
            throw new InvalidArgumentException("Entity class '{$entityClass}' does not exist.");
        }

        $repositoryClass = (new $entityClass())->repositoryClass ?? null;

        if (!$repositoryClass || !class_exists($repositoryClass)) {
            throw new InvalidArgumentException("Repository class '{$repositoryClass}' not found for entity '{$entityClass}'.");
        }

        return app($repositoryClass); // Laravel처럼 컨테이너에서 resolve
    }
}
