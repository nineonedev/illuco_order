<?php

namespace App\Domains\Order\Repositories\Documents;

use App\Domains\Order\Entities\Documents\ProformaInvoice;
use Framework\Database\ORM\Repositories\Repository;

class ProformaInvoiceRepository extends Repository
{
    public static function table(): string
    {
        return 'order_document_proforma_invoices';
    }

    public static function entityClass(): string
    {
        return ProformaInvoice::class;
    }
}
