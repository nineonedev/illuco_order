<?php

namespace App\Domains\User\Enums; 

class UserType 
{
    const ADMIN = 'admin'; 
    const DEALER = 'dealer'; 
    const EMPLOYEE = 'employee'; 

    const SALES = 'sales';

    public static function all(): array
    {
        return [
            self::ADMIN,
            self::DEALER,
            self::EMPLOYEE,
            self::SALES,
        ];
    }
}