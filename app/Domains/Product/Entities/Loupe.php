<?php

namespace App\Domains\Product\Entities;

use App\Domains\Product\Repositories\LoupeRepository;
use Framework\Database\ORM\Entities\Entity;

class Loupe extends Entity
{
    protected array $fillable = [
        'id',
        'type',
        'frame_type',
        'working_distance',

        'od_sph',
        'os_sph',

        'od_cyl',
        'os_cyl',

        'od_axis',
        'os_axis',

        'od_add',
        'os_add',

        'pd_right',
        'pd_left',
        'pd_total',
        'vertext_distance',
        'add_option'
    ];

    public static array $additionalOptions = [
        'prescription_lens' => [
            'name' => '처방렌즈',
            'price' => 30.00,
        ],
    ];

    public static array $modelSpecs = [
        'ITL-1025G' => [
            'frame_types' => [
                ['value' => 'frame1', 'label' => 'Frame 1'],
                ['value' => 'frame2', 'label' => 'Frame 2'],
                ['value' => 'sports', 'label' => 'Sports'],
                ['value' => 'frame4', 'label' => 'Frame 4'],
            ],
            'working_distance' => ['min' => 35, 'max' => 55],
        ],
        'ITL-1040P' => [
            'frame_types' => [
                ['value' => 'frame4', 'label' => 'Frame 4'],
            ],
            'working_distance' => ['min' => 45, 'max' => 65],
        ],
        'ITL-1030G' => [
            'frame_types' => [
                ['value' => 'frame1', 'label' => 'Frame 1'],
                ['value' => 'frame2', 'label' => 'Frame 2'],
                ['value' => 'sports', 'label' => 'Sports'],
                ['value' => 'frame4', 'label' => 'Frame 4'],
            ],
            'working_distance' => ['min' => 35, 'max' => 55],
        ],
        'ITL-1035G' => [
            'frame_types' => [
                ['value' => 'frame1', 'label' => 'Frame 1'],
                ['value' => 'frame2', 'label' => 'Frame 2'],
                ['value' => 'sports', 'label' => 'Sports'],
                ['value' => 'frame4', 'label' => 'Frame 4'],
            ],
            'working_distance' => ['min' => 35, 'max' => 55],
        ],
        'ITL-1045P' => [
            'frame_types' => [
                ['value' => 'frame4', 'label' => 'Frame 4'],
            ],
            'working_distance' => ['min' => 45, 'max' => 65],
        ],
        'ITL-1055P' => [
            'frame_types' => [
                ['value' => 'frame4', 'label' => 'Frame 4'],
            ],
            'working_distance' => ['min' => 45, 'max' => 65],
        ],
        'ITL-1065P' => [
            'frame_types' => [
                ['value' => 'frame4', 'label' => 'Frame 4'],
            ],
            'working_distance' => ['min' => 45, 'max' => 65],
        ],
        'IAL-1030' => [
            'frame_types' => [
                ['value' => 'frame4', 'label' => 'Frame 4'],
            ],
            'working_distance' => ['min' => 45, 'max' => 65],
        ],
        'IAL-1040' => [
            'frame_types' => [
                ['value' => 'frame4', 'label' => 'Frame 4'],
            ],
            'working_distance' => ['min' => 45, 'max' => 65],
        ],
        'IAL-1055' => [
            'frame_types' => [
                ['value' => 'frame4', 'label' => 'Frame 4'],
            ],
            'working_distance' => ['min' => 45, 'max' => 65],
        ],
        'IFL-1030G' => [
            'frame_types' => [
                ['value' => 'frame1', 'label' => 'Frame 1'],
            ],
            'working_distance' => ['min' => 35, 'max' => 55],
        ],
    ];

    public static function getSpecsForModel(string $model): array
    {
        return self::$modelSpecs[$model] ?? [];
    }

    public static function repositoryClass(): string
    {
        return LoupeRepository::class;
    }
}
