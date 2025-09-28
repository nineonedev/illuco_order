<?php

declare(strict_types=1);

namespace App\Domains\Product\Entities;

use App\Domains\Product\Repositories\LoupeSettingRepository;
use Framework\Database\ORM\Entities\Entity;

class LoupeSetting extends Entity
{
    /**
     * 명시적으로 테이블명을 고정하고 싶다면 선언(프레임워크 기본 네이밍과 다르면 필요)
     * @var string
     */
    protected string $table = 'loupe_settings';

    /**
     * 대량 할당 허용 컬럼
     * @var array<int, string>
     */
    protected array $fillable = [
        'id',
        'template_id',

        'vd_min',
        'vd_max',

        'fd_right_min',
        'fd_right_max',
        'fd_left_min',
        'fd_left_max',
        'fd_total_distance',

        'wd_min',
        'wd_max',

        'frame_color',

        'created_at',
        'updated_at',
    ];

    /**
     * 타입 캐스팅
     * - decimal은 기존 엔티티와 동일하게 'decimal' 사용
     * - frame_color는 JSON 배열이므로 'json' 또는 'array'로 캐스팅
     * @var array<string, string>
     */
    protected array $casts = [
        'id'              => 'int',
        'template_id'     => 'int',

        'vd_min'          => 'decimal',
        'vd_max'          => 'decimal',

        'fd_right_min'    => 'decimal',
        'fd_right_max'    => 'decimal',
        'fd_left_min'     => 'decimal',
        'fd_left_max'     => 'decimal',
        'fd_total_distance'=> 'decimal',

        'wd_min'          => 'decimal',
        'wd_max'          => 'decimal',

        'frame_color'     => 'json',   // 또는 'array' (프레임워크 캐스터 명에 맞춰 선택)
    ];

    /**
     * 연결된 Repository
     */
    public static function repositoryClass(): string
    {
        return LoupeSettingRepository::class;
    }

    // ───────────────────────────────────────────────────────────
    // 편의 메서드(프론트/서비스 계층에서 그대로 쓰기 좋게 변환)
    // ───────────────────────────────────────────────────────────

    /**
     * WD 범위(min/max)를 배열로 반환 (LoupeForm에서 기대하는 구조 예시)
     */
    public function getWorkingDistanceRange(): array
    {
        return [
            'min' => $this->wd_min !== null ? (float)$this->wd_min : null,
            'max' => $this->wd_max !== null ? (float)$this->wd_max : null,
        ];
    }

    /**
     * VD 범위를 배열로 반환
     */
    public function getVertexDistanceRange(): array
    {
        return [
            'min' => $this->vd_min !== null ? (float)$this->vd_min : null,
            'max' => $this->vd_max !== null ? (float)$this->vd_max : null,
        ];
    }

    /**
     * 각 눈의 far PD 범위 및 총합(표시/검증용)을 반환
     */
    public function getPdSpec(): array
    {
        return [
            'right' => [
                'min' => $this->fd_right_min !== null ? (float)$this->fd_right_min : null,
                'max' => $this->fd_right_max !== null ? (float)$this->fd_right_max : null,
            ],
            'left' => [
                'min' => $this->fd_left_min !== null ? (float)$this->fd_left_min : null,
                'max' => $this->fd_left_max !== null ? (float)$this->fd_left_max : null,
            ],
            'total' => $this->fd_total_distance !== null ? (float)$this->fd_total_distance : null,
        ];
    }

    /**
     * 프론트에서 바로 쓸 수 있는 attributes 페이로드로 변환
     * (LoupeForm가 기대하는 키 네이밍에 맞춤)
     */
    public function toAttributesPayload(): array
    {
        return [
            'working_distance' => $this->getWorkingDistanceRange(), // {min, max}
            'vd'               => $this->getVertexDistanceRange(),  // {min, max} (필요 시 사용)
            'fd'               => $this->getPdSpec(),               // {right:{min,max}, left:{min,max}, total}
            'frame_colors'     => $this->getFrameColorIds(),        // [1,2,3] (ID 배열)
        ];
    }

    /**
     * frame_color 컬러 ID 배열(JSON) 접근자
     * @return array<int,int>
     */
    public function getFrameColorIds(): array
    {
        $val = $this->frame_color;
        if (is_string($val)) {
            $decoded = json_decode($val, true);
            return is_array($decoded) ? array_values($decoded) : [];
        }
        if (is_array($val)) {
            return array_values($val);
        }
        return [];
    }

    /**
     * frame_color 설정자 - 배열을 넘기면 JSON으로 저장되도록 보정
     * @param array<int,int> $ids
     * @return $this
     */
    public function setFrameColorIds(array $ids): self
    {
        $this->frame_color = array_values($ids);
        return $this;
    }
}
