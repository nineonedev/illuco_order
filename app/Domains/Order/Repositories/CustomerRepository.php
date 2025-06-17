<?php

namespace App\Domains\Order\Repositories;

use App\Domains\Order\Entities\Customer;
use Framework\Database\ORM\Repositories\Repository;

class CustomerRepository extends Repository
{
    public static function table(): string
    {
        return 'customers';
    }

    public static function entityClass(): string
    {
        return Customer::class;
    }
}
