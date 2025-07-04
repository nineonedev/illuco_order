<?php

namespace App\Domains\Order\Entities\Documents;

use App\Domains\Order\Repositories\Documents\ProformaInvoiceRepository;
use Framework\Database\ORM\Entities\Entity;

class ProformaInvoice extends Entity
{
    protected array $fillable = [
        'id',
        'invoice_no',
        'invoice_date',
        'buyer_name',
        'buyer_address',
        'item_name',
        'item_model',
        'unit_price',
        'quantity',
        'amount',
        'remarks',
    ];

    protected array $casts = [
        'id'             => 'int',
        'invoice_no'     => 'string',
        'invoice_date'   => 'date',
        'buyer_name'     => 'string',
        'buyer_address'  => 'string',
        'item_name'      => 'string',
        'item_model'     => 'string',
        'unit_price'     => 'decimal',
        'quantity'       => 'int',
        'amount'         => 'decimal',
        'remarks'        => 'string',
    ];

    public static function repositoryClass(): string
    {
        return ProformaInvoiceRepository::class;
    }

    public static function alias(): string
    {
        return 'proforma_invoice';
    }

    public static function code(): string
    {
        return 'PI';
    }
}
