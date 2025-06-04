<?php

namespace Framework\Database\Model;

use Framework\Database\Model\Entities\Entity;
use Framework\Database\Contracts\RepositoryInterface;
use Framework\Database\Model\Repositories\NullRepository;

class NullModel extends Model
{
    public function __construct()
    {
        $this->entity = new Entity(); // 빈 엔티티
    }

    public function entityClass(): string
    {
        return Entity::class;
    }

    public function repositoryClass(): string
    {
        return NullRepository::class;
    }

    public function save(): bool
    {
        return false;
    }

    public function delete(): bool
    {
        return false;
    }

    public function update(array $attributes): bool
    {
        return false;
    }

    public function restore(): bool
    {
        return false;
    }

    public function exists(): bool
    {
        return false;
    }
}
