<?php

namespace App\Domains\Order\Repositories\Documents;

use App\Domains\Order\Entities\Documents\CommercialInvoice;
use Framework\Database\ORM\Repositories\Repository;

class CommercialInvoiceRepository extends Repository
{
    public static function table(): string
    {
        return 'order_document_commercial_invoices';
    }

    public static function entityClass(): string
    {
        return CommercialInvoice::class;
    }
}
