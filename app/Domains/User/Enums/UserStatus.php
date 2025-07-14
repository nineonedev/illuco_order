<?php

namespace App\Domains\User\Enums;

class UserStatus
{
    const ACTIVE = 'active';
    const INACTIVE = 'inactive';

    public static function all(): array
    {
        return [
            self::ACTIVE,
            self::INACTIVE,
        ];
    }

    public static function labels(): array
    {
        return [
            self::ACTIVE => '활성',
            self::INACTIVE => '비활성',
        ];
    }
}
