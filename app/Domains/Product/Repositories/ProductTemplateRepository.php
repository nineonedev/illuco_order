<?php

namespace App\Domains\Product\Repositories;

use App\Domains\Product\Entities\ProductTemplate;
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
