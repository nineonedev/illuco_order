<?php

namespace App\User\Entities;

use Framework\Database\Model\Entities\Entity;

class UserEntity extends Entity
{
    protected string $primaryKey = 'id';

    protected array $fillable = [
        'name',
        'email'
    ];

    protected function defineCasts(): array
    {
        return [
            'is_active' => 'bool',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }
}
