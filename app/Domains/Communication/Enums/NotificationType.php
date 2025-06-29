<?php

namespace App\Domains\Communication\Enums;

class NotificationType 
{
    const ORDER_STATUS_CHANGED = 'order_status_changed'; 
    
    const CLAIM_STATUS_CHANGED = 'claim_status_changed'; 

    const NOTICE_CREATED = 'notice_created';

    public static function all(): array
    {
        return [
            self::ORDER_STATUS_CHANGED,
            self::CLAIM_STATUS_CHANGED, 
            self::NOTICE_CREATED,
        ];
    }
}