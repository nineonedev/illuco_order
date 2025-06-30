<?php

namespace App\Domains\User\Enums; 

class UserType 
{
    const ADMIN = 'admin'; 
    const DEALER = 'dealer'; 
    const EMPLOYEE = 'employee'; 

    public static function all(): array
    {
        return [
            self::ADMIN,
            self::DEALER,
            self::EMPLOYEE,
        ];
    }
}