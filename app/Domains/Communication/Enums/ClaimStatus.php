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
}