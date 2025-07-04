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
            'wireless_colors' => [
                [
                    'value' => 'pink',
                    'label' => '핑크',
                ],
                [
                    'value' => 'gray',
                    'label' => '그레이',
                ],
                [
                    'value' => 'gold',
                    'label' => '골드',
                ],
                [
                    'value' => 'silver',
                    'label' => '실버',
                ],

            ]
        ],
        'IHL-1000' => [
            'wireless_colors' => null,
        ],
    ];
}
