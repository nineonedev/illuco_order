<?php

namespace App\Domains\Product\Entities;

use App\Domains\Product\Repositories\LoupeFrameColorRepository;
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

    // Loupe 클래스 내부에 추가/수정

    public static function renderTable(string $type, array $attributes): string
    {
        // custom-made 가 아니면 테이블 없음
        if (($attributes['type'] ?? '') !== 'custom-made') {
            return '';
        }

        $html = '<div class="no-order-options__block">';
        // ---------- 첫 번째: 시력정보 테이블 ----------
        $html .= '
            <table class="no-order-option-table ">
                <thead>
                    <tr>
                        <th scope="col"><span class="--blind">Eye</span></th>
                        <th scope="col">SPH</th>
                        <th scope="col">CYL</th>
                        <th scope="col">Axis</th>
                        <th scope="col">Add</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th scope="row">OD</th>
                        <td>' . e($attributes['od_sph'] ?? '-') . '</td>
                        <td>' . e($attributes['od_cyl'] ?? '-') . '</td>
                        <td>' . e($attributes['od_axis'] ?? '-') . '</td>
                        <td>' . e($attributes['od_add'] ?? '-') . '</td>
                    </tr>
                    <tr>
                        <th scope="row">OS</th>
                        <td>' . e($attributes['os_sph'] ?? '-') . '</td>
                        <td>' . e($attributes['os_cyl'] ?? '-') . '</td>
                        <td>' . e($attributes['os_axis'] ?? '-') . '</td>
                        <td>' . e($attributes['os_add'] ?? '-') . '</td>
                    </tr>
                </tbody>
            </table>
        ';

        // ---------- 두 번째: 계산 요약 테이블 ----------
        $fmtSigned = function ($v, int $dec = 2) {
            if ($v === null || $v === '') return '-';
            $v = (float)$v;
            return sprintf('%+.' . $dec . 'f', $v);
        };
        $fmtAxis = function ($v) {
            if ($v === null || $v === '') return '-';
            return (string) intval($v) . '°';
        };

        // 입력값
        $odS = isset($attributes['od_sph']) ? (float)$attributes['od_sph'] : null;
        $odC = isset($attributes['od_cyl']) ? (float)$attributes['od_cyl'] : null;
        $odA = isset($attributes['od_axis']) ? (int)$attributes['od_axis'] : null;
        $odAdd = isset($attributes['od_add']) ? (float)$attributes['od_add'] : null;

        $osS = isset($attributes['os_sph']) ? (float)$attributes['os_sph'] : null;
        $osC = isset($attributes['os_cyl']) ? (float)$attributes['os_cyl'] : null;
        $osA = isset($attributes['os_axis']) ? (int)$attributes['os_axis'] : null;
        $osAdd = isset($attributes['os_add']) ? (float)$attributes['os_add'] : null;

        $opt = $attributes['add_option'] ?? null; // ignore | include | zero_diopter

        // 모렌즈(ADD 포함 규칙 반영)
        // 기본: 처방 그대로
        $molOd = ['s' => $odS, 'c' => $odC, 'a' => $odA, 'add' => null];
        $molOs = ['s' => $osS, 'c' => $osC, 'a' => $osA, 'add' => null];

        if ($opt === 'zero_diopter') {
            // 안경 미착용자: 모두 0
            $molOd = ['s' => 0, 'c' => 0, 'a' => 0, 'add' => null];
            $molOs = ['s' => 0, 'c' => 0, 'a' => 0, 'add' => null];

        } elseif ($opt === 'include') {
            // 모렌즈에 ADD 반영: 규칙표에 따른 S 가산치 적용
            $deltaOd = self::addToMorenzDelta($odAdd);
            $deltaOs = self::addToMorenzDelta($osAdd);

            $molOd['s'] = ($odS !== null && $deltaOd !== null) ? $odS + $deltaOd : $odS;
            $molOs['s'] = ($osS !== null && $deltaOs !== null) ? $osS + $deltaOs : $osS;

            // 표의 ADD 칸에도 보여주려면 원래 처방 ADD를 노출
            $molOd['add'] = $odAdd;
            $molOs['add'] = $osAdd;

        } else {
            // ignore 등: S 그대로, ADD 표시는 비움
            $molOd['add'] = null;
            $molOs['add'] = null;
        }

        // 자렌즈(기본식: S + ADD)
        $jarOd = ['s' => ($odS !== null && $odAdd !== null) ? $odS + $odAdd : null, 'c' => $odC, 'a' => $odA, 'add' => null];
        $jarOs = ['s' => ($osS !== null && $osAdd !== null) ? $osS + $osAdd : null, 'c' => $osC, 'a' => $osA, 'add' => null];

        $html .= '
            <table class="no-order-option-table no-order-option-table--summary ">
                <thead>
                    <tr>
                        <th>렌즈</th>
                        <th colspan="4">OD, R</th>
                        <th colspan="4">OS, L</th>
                    </tr>
                    <tr>
                        <th></th>
                        <th>S</th><th>C</th><th>A</th><th>ADD</th>
                        <th>S</th><th>C</th><th>A</th><th>ADD</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th scope="row">처방전</th>
                        <td>'.$fmtSigned($odS).'</td><td>'.$fmtSigned($odC).'</td><td>'.$fmtAxis($odA).'</td><td>'.$fmtSigned($odAdd).'</td>
                        <td>'.$fmtSigned($osS).'</td><td>'.$fmtSigned($osC).'</td><td>'.$fmtAxis($osA).'</td><td>'.$fmtSigned($osAdd).'</td>
                    </tr>
                    <tr>
                        <th scope="row">모렌즈</th>
                        <td>'.$fmtSigned($molOd["s"]).'</td><td>'.$fmtSigned($molOd["c"]).'</td><td>'.$fmtAxis($molOd["a"]).'</td><td>'.$fmtSigned($molOd["add"]).'</td>
                        <td>'.$fmtSigned($molOs["s"]).'</td><td>'.$fmtSigned($molOs["c"]).'</td><td>'.$fmtAxis($molOs["a"]).'</td><td>'.$fmtSigned($molOs["add"]).'</td>
                    </tr>
                    <tr>
                        <th scope="row">자렌즈</th>
                        <td>'.$fmtSigned($jarOd["s"]).'</td><td>'.$fmtSigned($jarOd["c"]).'</td><td>'.$fmtAxis($jarOd["a"]).'</td><td>–</td>
                        <td>'.$fmtSigned($jarOs["s"]).'</td><td>'.$fmtSigned($jarOs["c"]).'</td><td>'.$fmtAxis($jarOs["a"]).'</td><td>–</td>
                    </tr>
                </tbody>
            </table>
        ';
        
        $html .= '</div>';

        return $html;
    }

    private static function addToMorenzDelta($add): ?float
    {
        if ($add === null || $add === '' ) return null;
        $add = (float) $add;

        if ($add < 1.0) {
            return 0.0;
        } elseif ($add < 2.5) {
            return 0.5;
        }
        return 0.75;
    }

    public static function renderTag(string $type, array $attributes): string
    {
        $html = '';

        // 공통 제외 필드(시력값은 태그에서 제외)
        $exclude = ['id', 'od_sph', 'os_sph', 'od_cyl', 'os_cyl', 'od_axis', 'os_axis', 'od_add', 'os_add'];

        // ready-made 이면 시력 관련 필드도 태그에서 제외
        if (($attributes['type'] ?? '') === 'ready-made') {
            $exclude = array_merge($exclude, [
                'pd_right', 'pd_left', 'pd_total',
                'vertex_distance', 'add_option',
            ]);
        }

        foreach ($attributes as $field => $value) {
            if (in_array($field, $exclude, true)) {
                continue;
            }
            if ($value === null || $value === '') continue;

            if ($field === 'add_option') {
                $value = self::LABELS['add_option_'.$value] ?? '-';
            }

            if ($field === 'frame_type') {
                $frameColor = LoupeFrameColorRepository::make()->query()->where('code', '=', $value)->first();
                if ($frameColor) {
                    $value = $frameColor->name; 
                }
            }

            $html .= '
                <div class="no-order-option-tag">
                    <span class="label">' . lang('system.' . $type . '.' . $field) . '</span>
                    <span class="value">' . e($value) . '</span>
                </div>
            ';
        }

        return $html;
    }


}
