<?php

namespace App\Domains\Product\Entities;

use App\Domains\Product\Repositories\HeadlightRepository;
use Framework\Database\ORM\Entities\Entity;

class Headlight extends Entity
{
    protected array $fillable = [
        'id',
        'wireless_color',
        'engraving_text',
    ];

    public static function repositoryClass(): string
    {
        return HeadlightRepository::class;
    }

    const MODEL_SPECS = [
        'IHL-2000' => [
            'colors' => [
                'pink',
                'gray',
                'gold',
                'silver'
            ]
        ],
        'IHL-1000' => [
        ],
    ];
}
