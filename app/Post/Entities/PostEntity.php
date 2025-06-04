<?php

namespace App\Post\Entities;

use Framework\Database\Model\Entities\Entity;

class PostEntity extends Entity
{
    protected string $primaryKey = 'id';

    protected function defineCasts(): array
    {
        return [
            'published' => 'bool',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }
}
