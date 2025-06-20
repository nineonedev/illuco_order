<?php

namespace App\Domains\Order\Repositories;

use App\Domains\Order\Entities\CartItem;
use Framework\Database\ORM\Repositories\Repository;

class CartItemRepository extends Repository
{
    public static function table(): string
    {
        return 'cart_items';
    }

    public static function entityClass(): string
    {
        return CartItem::class;
    }
}
