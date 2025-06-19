<?php

namespace App\Domains\Order\Repositories;

use App\Domains\Order\Entities\Cart;
use Framework\Database\ORM\Repositories\Repository;

class CartItemRepository extends Repository
{
    public static function table(): string
    {
        return 'cart_items';
    }

    public static function entityClass(): string
    {
        return Cart::class;
    }
}
