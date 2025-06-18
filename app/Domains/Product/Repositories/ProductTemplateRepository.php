<?php

namespace App\Domains\Order\Repositories;

use App\Domains\Order\Entities\ProductTemplate;
use Framework\Database\ORM\Repositories\Repository;

class ProductTemplateRepository extends Repository
{
    public static function table(): string
    {
        return 'product_templates';
    }

    public static function entityClass(): string
    {
        return ProductTemplate::class;
    }
}
