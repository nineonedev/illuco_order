<?php

namespace App\Domains\Order\Repositories\Documents;

use App\Domains\Order\Entities\Documents\ProductRequest;
use Framework\Database\ORM\Repositories\Repository;

class ProductRequestRepository extends Repository
{
    public static function table(): string
    {
        return 'order_document_product_requests';
    }

    public static function entityClass(): string
    {
        return ProductRequest::class;
    }
}
