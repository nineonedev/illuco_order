<?php

declare(strict_types=1);

namespace App\Domains\Product\Repositories;

use App\Domains\Product\Entities\LoupeFrameColor;
use Framework\Database\ORM\Repositories\Repository;

class LoupeFrameColorRepository extends Repository
{
    public static function table(): string
    {
        return 'loupe_frame_colors';
    }

    public static function entityClass(): string
    {
        return LoupeFrameColor::class;
    }

    /**
     * 활성 컬러 목록(정렬 포함)
     * @return LoupeFrameColor[]
     */
    public function listActiveOrdered(): array
    {
        return $this->query()
            ->where('is_active', '=', 1)
            ->orderBy('sort_order', 'ASC')
            ->orderBy('id', 'ASC')
            ->get();
    }

    /**
     * ID 배열로 다건 조회 (입력 순서 보장 X)
     * @return LoupeFrameColor[]
     */
    public function findManyByIds(array $ids): array
    {
        if (empty($ids)) return [];
        return $this->query()
            ->whereIn('id', $ids)
            ->get();
    }

    /**
     * UI 옵션 형태로 반환 (활성 + 정렬)
     * e.g. [['label'=>'Black','value'=>1,'hex'=>'#000000'], ...]
     */
    public function options(): array
    {
        return array_map(
            fn(LoupeFrameColor $c) => $c->toOption(),
            $this->listActiveOrdered()
        );
    }

    /**
     * 코드로 조회 (없으면 null)
     */
    public function findByCode(string $code): ?LoupeFrameColor
    {
        /** @var LoupeFrameColor|null */
        return $this->query()
            ->where('code', '=', $code)
            ->first();
    }
}
