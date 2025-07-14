<?php

namespace App\Domains\Communication\Enums;

class ClaimStatus
{
    const RECEIVED = 'received'; 
    const PROCESSING = 'processing'; 
    const COMPLETED = 'completed';

    public static function all(): array
    {
        return [
            self::RECEIVED,
            self::PROCESSING,
            self::COMPLETED,
        ];
    }

    public static function labels(): array
    {
        return [
            self::RECEIVED => '접수됨',
            self::PROCESSING => '처리중',
            self::COMPLETED => '완료',
        ];
    }
}
