<?php

declare(strict_types=1);

namespace App\Domains\Product\Repositories;

use App\Domains\Product\Entities\LoupeSetting;
use Framework\Database\ORM\Repositories\Repository;

class LoupeSettingRepository extends Repository
{
    public static function table(): string
    {
        return 'loupe_settings';
    }

    public static function entityClass(): string
    {
        return LoupeSetting::class;
    }

    /**
     * template_id로 단일 설정 조회
     */
    public function findByTemplateId(int $templateId): ?LoupeSetting
    {
        /** @var LoupeSetting|null */
        return $this->query()
            ->where('template_id', '=', $templateId)
            ->first();
    }

    /**
     * 여러 template_id 한번에 조회 (키를 template_id로 매핑)
     * @return array<int, LoupeSetting>
     */
    public function getByTemplateIds(array $templateIds): array
    {
        if (empty($templateIds)) {
            return [];
        }

        $rows = $this->query()
            ->whereIn('template_id', $templateIds)
            ->get();

        $map = [];
        foreach ($rows as $row) {
            /** @var LoupeSetting $row */
            $map[(int)$row->template_id] = $row;
        }
        return $map;
    }

    /**
     * template_id 기준 업서트
     */
    public function upsertByTemplateId(int $templateId, array $attributes): LoupeSetting
    {
        $model = $this->findByTemplateId($templateId)
            ?? new LoupeSetting(['template_id' => $templateId]);

        $model->fill($attributes);
        $this->save($model);

        return $model;
    }
}
