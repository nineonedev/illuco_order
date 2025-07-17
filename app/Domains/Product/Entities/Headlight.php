<?php

namespace App\Domains\Product\Entities;

use App\Domains\Product\Repositories\HeadlightRepository;
use Framework\Database\ORM\Entities\Entity;

class Headlight extends Entity
{
    protected array $fillable = [
        'id',
        'type',
        'wireless_color',
        'engraving_text',
    ];

    public static function repositoryClass(): string
    {
        return HeadlightRepository::class;
    }

    const LABELS = [
        'wireless_color' => '무선 컬러',
        'wireless_color_pink'   => '핑크',
        'wireless_color_gray'   => '그레이',
        'wireless_color_gold'   => '골드',
        'wireless_color_silver' => '실버',

        
        'engraving_text' => '각인 내용',
    ];

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

    public static function renderTag(string $type, array $attributes): string
    {
        $html = '';

        foreach ($attributes as $field => $value) {
            if (in_array($field, ['id', 'type'])) continue;  

            if (!$value) continue;
            
            $html .= '
                <div class="no-order-option-tag">
                    <span class="label">'.lang('system.' . $type . '.' . $field).'</span>
                    <span class="value">'. e($value) .'</span>
                </div>
            ';
        }

        return $html;
    }
}
