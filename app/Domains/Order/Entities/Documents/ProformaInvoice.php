<?php

namespace App\Domains\Order\Entities\Documents;

use App\Domains\Order\Repositories\Documents\ProformaInvoiceRepository;
use Framework\Database\ORM\Entities\Entity;

class ProformaInvoice extends Entity
{
    protected array $fillable = [
        'id',
        'document_no',
        'purchase_order_no',
        'invoice_no',
        'invoice_date',
        'buyer_name',
        'buyer_address',
        'buyer_tel',
        'buyer_attn',
        'buyer_email',
        'country_of_origin',
        'currency',
        'salesperson_name',
        'salesperson_tel',
        'salesperson_email',
        'estimated_date_of_delivery',
        'price_terms',
        'payment_terms',
        'shipment_by',
        'hs_code',
        'freight_charge',
        'bank_beneficiary',
        'bank_name',
        'bank_address',
        'bank_swift_code',
        'bank_account_no',
        'note',
    ];

    protected array $casts = [
        'id'                          => 'int',
        'document_no'                 => 'string',
        'purchase_order_no'           => 'string',
        'invoice_no'                  => 'string',
        'invoice_date'                => 'date',
        'buyer_name'                  => 'string',
        'buyer_address'               => 'string',
        'buyer_tel'                   => 'string',
        'buyer_attn'                  => 'string',
        'buyer_email'                 => 'string',
        'country_of_origin'           => 'string',
        'currency'                    => 'string',
        'salesperson_name'            => 'string',
        'salesperson_tel'             => 'string',
        'salesperson_email'           => 'string',
        'estimated_date_of_delivery'  => 'string',
        'price_terms'                 => 'string',
        'payment_terms'               => 'string',
        'shipment_by'                 => 'string',
        'hs_code'                     => 'string',
        'freight_charge'              => 'decimal',
        'bank_beneficiary'            => 'string',
        'bank_name'                   => 'string',
        'bank_address'                => 'string',
        'bank_swift_code'             => 'string',
        'bank_account_no'             => 'string',
        'note'                        => 'string',
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
