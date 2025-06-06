<?php

namespace Framework\Database\ORM\Traits;

use DateTime;

trait SoftDeletes
{
    /**
     * @var string
     */
    protected $softDeleteColumn = 'deleted_at';

    public function markDeleted(): void
    {
        $this->set($this->softDeleteColumn, (new DateTime())->format('Y-m-d H:i:s'));
    }

    public function restore(): void
    {
        $this->set($this->softDeleteColumn, null);
    }

    public function isDeleted(): bool
    {
        return !is_null($this->get($this->softDeleteColumn));
    }

    public function deletedAt(): ?string
    {
        $value = $this->get($this->softDeleteColumn);
        return is_string($value) ? $value : null;
    }

    public function getSoftDeleteColumn(): string
    {
        return $this->softDeleteColumn;
    }
}
