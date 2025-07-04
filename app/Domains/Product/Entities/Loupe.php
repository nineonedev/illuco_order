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
        'vertex_distance',
        'add_option',

        'engraving_text',
    ];

    protected array $casts = [
        'id' => 'int',
        'working_distance' => 'decimal',
        'od_sph' => 'decimal',
        'os_sph' => 'decimal',

        'od_cyl' => 'decimal',
        'os_cyl' => 'decimal',

        'od_axis' => 'int',
        'os_axis' => 'int',

        'od_add' => 'decimal',
        'os_add' => 'decimal',

        'pd_right' => 'decimal',
        'pd_left' => 'decimal',
        'pd_total' => 'decimal',
        'vertex_distance' => 'decimal',
    ];

    const PRECISON_LENS = 'PR-LENS-30';

    const WORKING_DISTANCES = [
        'WD_35_55' => [
            'label' => '35~55cm',
            'min' => 35,
            'max' => 55,
        ],
        'WD_45_65' => [
            'label' => '45~65cm',
            'min' => 45,
            'max' => 65,
        ],
    ];

    const FRAMES = [
        'FRAME_1' => [
            'label' => 'Frame 1',
            'value' => 'frame1',
        ],
        'FRAME_2' => [
            'label' => 'Frame 2',
            'value' => 'frame2',
        ],
        'SPORTS' => [
            'label' => 'Sports',
            'value' => 'sports',
        ],
        'FRAME_4' => [
            'label' => 'Frame 4',
            'value' => 'frame4',
        ],
    ];

    const LABELS = [
        'type' => '형태',
        'type_ready-made' => 'Ready-made',
        'type_custom-made' => 'Custom-made',

        // frame values
        'frame_type' => '안경테',
        'frame_type_frame1' => 'Frame 1',
        'frame_type_frame2' => 'Frame 2',
        'frame_type_sports' => 'Sports',
        'frame_type_frame4' => 'Frame 4',

        'working_distance' => 'WD (단위: Cm)',

        'od_sph' => 'OD SPH',
        'os_sph' => 'OS SPH',

        'od_cyl' => 'OD CYL',
        'os_cyl' => 'OS CYL',

        'od_axis' => 'OD Axis',
        'os_axis' => 'OS Axis',

        'od_add' => 'OD Add',
        'os_add' => 'OS Add',

        'pd_right' => 'Far PD, RIGHT (단위: mm)',
        'pd_left' => 'Far PD, LEFT (단위: mm)',
        'pd_total' => 'Total PD (단위: mm)',
        'vertex_distance' => 'VD (단위: mm)',
        

        'add_option' => '모렌즈 ADD 값 선택',
        'add_option_include' => '모렌즈에 ADD값 포함 요청 - 근거리용',
        'add_option_ignore' => '모렌즈에 ADD값 무시 요청 - 원용',
        'add_option_zero_diopter' => '모렌즈 0 디옵터 적용 - 안경 미착용자',

        'engraving_text' => '각인 내용',
    ];

    const MODEL_SPECS = [
        'ITL-1025G' => [
            'frame_type' => [
                self::FRAMES['FRAME_1'],
                self::FRAMES['FRAME_2'],
                self::FRAMES['SPORTS'],
                self::FRAMES['FRAME_4'],
            ],
            'working_distance' => self::WORKING_DISTANCES['WD_35_55'],
        ],
        'ITL-1040P' => [
            'frame_type' => [
                self::FRAMES['FRAME_4'],
            ],
            'working_distance' => self::WORKING_DISTANCES['WD_45_65'],
        ],
        'ITL-1030G' => [
            'frame_type' => [
                self::FRAMES['FRAME_1'],
                self::FRAMES['FRAME_2'],
                self::FRAMES['SPORTS'],
                self::FRAMES['FRAME_4'],
            ],
            'working_distance' => self::WORKING_DISTANCES['WD_35_55'],
        ],
        'ITL-1035G' => [
            'frame_type' => [
                self::FRAMES['FRAME_1'],
                self::FRAMES['FRAME_2'],
                self::FRAMES['SPORTS'],
                self::FRAMES['FRAME_4'],
            ],
            'working_distance' => self::WORKING_DISTANCES['WD_35_55'],
        ],
        'ITL-1045P' => [
            'frame_type' => [
                self::FRAMES['FRAME_4'],
            ],
            'working_distance' => self::WORKING_DISTANCES['WD_45_65'],
        ],
        'ITL-1055P' => [
            'frame_type' => [
                self::FRAMES['FRAME_4'],
            ],
            'working_distance' => self::WORKING_DISTANCES['WD_45_65'],
        ],
        'ITL-1065P' => [
            'frame_type' => [
                self::FRAMES['FRAME_4'],
            ],
            'working_distance' => self::WORKING_DISTANCES['WD_45_65'],
        ],
        'IAL-1030' => [
            'frame_type' => [
                self::FRAMES['FRAME_4'],
            ],
            'working_distance' => self::WORKING_DISTANCES['WD_45_65'],
        ],
        'IAL-1040' => [
            'frame_type' => [
                self::FRAMES['FRAME_4'],
            ],
            'working_distance' => self::WORKING_DISTANCES['WD_45_65'],
        ],
        'IAL-1055' => [
            'frame_type' => [
                self::FRAMES['FRAME_4'],
            ],
            'working_distance' => self::WORKING_DISTANCES['WD_45_65'],
        ],
        'IFL-1030G' => [
            'frame_type' => [
                self::FRAMES['FRAME_1'],
            ],
            'working_distance' => self::WORKING_DISTANCES['WD_35_55'],
        ],
    ];

    public static function getSpecsForModel(string $model): array
    {
        return self::MODEL_SPECS[$model] ?? [];
    }

    public static function repositoryClass(): string
    {
        return LoupeRepository::class;
    }
}
