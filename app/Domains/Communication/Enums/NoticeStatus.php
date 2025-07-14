<?php

namespace App\Domains\Communication\Enums;

class NoticeStatus
{
    /**
     * 노출 X
     * 임시 저장 상태
     */
    const DRAFT = 'draft';

    /**
     * 노출 O
     * 공개됨
     */
    const PUBLISHED = 'published';

    /**
     * 노출 X
     * 보관 상태 (노출하지 않지만 기록은 남김)
     */
    const ARCHIVED = 'archived';

    /**
     * 노출 X
     * 예약발행. 특정 기간 동안만 노출됨.
     */
    const SCHEDULED = 'scheduled';

    /**
     * 모든 상태 값 배열 반환
     *
     * @return array
     */
    public static function all(): array
    {
        return [
            self::DRAFT,
            self::PUBLISHED,
            self::ARCHIVED,
            self::SCHEDULED,
        ];
    }
    
    /**
     * 상태값 라벨 매핑
     *
     * @return array
     */
    public static function labels(): array
    {
        return [
            self::DRAFT => '임시저장',
            self::PUBLISHED => '공개',
            self::ARCHIVED => '보관',
            self::SCHEDULED => '예약발행',
        ];
    }
}
