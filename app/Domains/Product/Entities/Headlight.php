<?php

namespace App\Domains\Product\Entities;

use App\Domains\Product\Repositories\HeadlightRepository;
use Framework\Database\ORM\Entities\Entity;

class Headlight extends Entity
{
    protected array $fillable = [
        'id',
        'wireless_color',
    ];

    public static function repositoryClass(): string
    {
        return HeadlightRepository::class;
    }
}
