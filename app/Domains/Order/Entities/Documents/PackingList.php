<?php

namespace App\Domains\Order\Entities\Documents;

use App\Domains\Order\Repositories\Documents\PackingListRepository;
use Framework\Database\ORM\Entities\Entity;

class PackingList extends Entity
{
    protected array $fillable = [
        'id',
        'packing_list_no',
        'packing_date',
        'buyer_name',
        'box_no',
        'product_name',
        'product_model',
        'quantity',
        'net_weight',
        'gross_weight',
        'volume',
        'remarks',
    ];

    protected array $casts = [
        'id'              => 'int',
        'packing_list_no' => 'string',
        'packing_date'    => 'date',
        'buyer_name'      => 'string',
        'box_no'          => 'string',
        'product_name'    => 'string',
        'product_model'   => 'string',
        'quantity'        => 'int',
        'net_weight'      => 'decimal',
        'gross_weight'    => 'decimal',
        'volume'          => 'string',
        'remarks'         => 'string',
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
