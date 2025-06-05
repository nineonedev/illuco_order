<?php

namespace Framework\Database\Model\Entities;

use DateTime;

class SoftDeletes
{
    protected Entity $entity;

    /**
     * @var string
     */
    protected $column = 'deleted_at';

    public function __construct(Entity $entity, string $column = 'deleted_at')
    {
        $this->entity = $entity;
        $this->column = $column;
    }

    public function markDeleted(): void
    {
        $this->entity->set($this->column, (new DateTime())->format('Y-m-d H:i:s'));
    }

    public function restore(): void
    {
        $this->entity->set($this->column, null);
    }

    public function isDeleted(): bool
    {
        return !is_null($this->entity->get($this->column));
    }

    public function deletedAt(): ?string
    {
        $value = $this->entity->get($this->column);
        return is_string($value) ? $value : null;
    }

    public function getColumn(): string
    {
        return $this->column;
    }

    public function getEntity(): Entity
    {
        return $this->entity;
    }
}
