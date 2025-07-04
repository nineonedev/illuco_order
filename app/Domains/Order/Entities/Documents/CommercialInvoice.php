<?php

namespace App\Domains\Order\Entities\Documents;

use App\Domains\Order\Repositories\Documents\CommercialInvoiceRepository;
use Framework\Database\ORM\Entities\Entity;

class CommercialInvoice extends Entity
{
    protected array $fillable = [
        'id',
        'invoice_no',
        'invoice_date',
        'buyer_name',
        'buyer_address',
        'product_name',
        'product_model',
        'unit_price',
        'quantity',
        'amount',
        'total_amount',
        'remarks',
    ];

    protected array $casts = [
        'id'             => 'int',
        'invoice_no'     => 'string',
        'invoice_date'   => 'date',
        'buyer_name'     => 'string',
        'buyer_address'  => 'string',
        'product_name'   => 'string',
        'product_model'  => 'string',
        'unit_price'     => 'decimal',
        'quantity'       => 'int',
        'amount'         => 'decimal',
        'total_amount'   => 'decimal',
        'remarks'        => 'string',
    ];

    public static function repositoryClass(): string
    {
        return CommercialInvoiceRepository::class;
    }


    public static function alias(): string
    {
        return 'commercial_invoice';
    }

    public static function code(): string
    {
        return 'CI';
    }
}
