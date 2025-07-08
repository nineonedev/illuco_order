<?php

namespace App\Domains\Order\Entities\Documents;

use App\Domains\Order\Repositories\Documents\PackingListRepository;
use Framework\Database\ORM\Entities\Entity;

class PackingList extends Entity
{
    protected array $fillable = [
        'id',
        'bill_to_name',
        'bill_to_address',
        'bill_to_tel',
        'bill_to_attn',
        'bill_to_email',

        'ship_to_name',
        'ship_to_address',
        'ship_to_tel',
        'ship_to_attn',
        'ship_to_email',

        'ref_no',
        'packing_date',
        'pi_no',
        'po_no',

        'carrier',
        'estimated_delivery_date',
        'payment_terms',
        'price_terms',
        'country_of_origin',

        'packing_details',
        'hs_code',
    ];

    protected array $casts = [
        'id'                          => 'int',
        'bill_to_name'                => 'string',
        'bill_to_address'             => 'string',
        'bill_to_tel'                 => 'string',
        'bill_to_attn'                => 'string',
        'bill_to_email'               => 'string',

        'ship_to_name'                => 'string',
        'ship_to_address'             => 'string',
        'ship_to_tel'                 => 'string',
        'ship_to_attn'                => 'string',
        'ship_to_email'               => 'string',

        'ref_no'                      => 'string',
        'packing_date'                => 'date',
        'pi_no'                       => 'string',
        'po_no'                       => 'string',

        'carrier'                     => 'string',
        'estimated_delivery_date'     => 'string',
        'payment_terms'               => 'string',
        'price_terms'                 => 'string',
        'country_of_origin'           => 'string',

        'packing_details'             => 'string',
        'hs_code'                     => 'string',
    ];

    public static function repositoryClass(): string
    {
        return PackingListRepository::class;
    }

    public static function alias(): string
    {
        return 'packing_list';
    }

    public static function code(): string
    {
        return 'PL';
    }
}
