<?php

declare(strict_types=1);

namespace App\Domains\Product\Entities;

use App\Domains\Product\Repositories\LoupeFrameColorRepository;
use Framework\Database\ORM\Entities\Entity;

class LoupeFrameColor extends Entity
{
    protected array $fillable = [
        'id',
        'name',        // 표시용 이름 (예: Black, Navy 등)
        'code',        // 내부 코드 (예: BLK, NVY)
        'hex',         // 색상 HEX (예: #000000)
        'is_active',   // 사용 여부
        'sort_order',  // 정렬용
        'meta',        // 확장 JSON
        'created_at',
        'updated_at',
    ];

    protected array $casts = [
        'id'         => 'int',
        'is_active'  => 'bool',
        'sort_order' => 'int',
        'meta'       => 'json',
    ];

    public static function repositoryClass(): string
    {
        return LoupeFrameColorRepository::class;
    }

    /**
     * 셀렉트/라디오 렌더용 옵션 형태
     * e.g. ['label' => 'Black', 'value' => 1, 'hex' => '#000000']
     */
    public function toOption(): array
    {
        return [
            'label' => $this->name,
            'value' => $this->id,
            'hex'   => $this->hex,
            'code'  => $this->code,
        ];
    }
}
