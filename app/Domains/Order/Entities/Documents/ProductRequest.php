<?php

namespace App\Domains\Order\Entities\Documents;

use App\Domains\Order\Repositories\Documents\ProductRequestRepository;
use Framework\Database\ORM\Entities\Entity;

class ProductRequest extends Entity
{
    protected array $fillable = [
        'id',
        'country',
        'customer_name',
        'created_date',
        'delivery_date',
        'manager_name',
        'document_no',
        'box1_no',
        'box1_weight',
        'box1_size',
        'box2_no',
        'box2_weight',
        'box2_size',
        'box3_no',
        'box3_weight',
        'box3_size',
        'box4_no',
        'box4_weight',
        'box4_size',
        'box5_no',
        'box5_weight',
        'box5_size',
        'note',
    ];

    protected array $casts = [
        'id'              => 'int',
        'country'         => 'string',
        'customer_name'   => 'string',
        'created_date'    => 'date',
        'delivery_date'   => 'date',
        'manager_name'    => 'string',
        'document_no'     => 'string',
        'box1_no'         => 'string',
        'box1_weight'     => 'string',
        'box1_size'       => 'string',
        'box2_no'         => 'string',
        'box2_weight'     => 'string',
        'box2_size'       => 'string',
        'box3_no'         => 'string',
        'box3_weight'     => 'string',
        'box3_size'       => 'string',
        'box4_no'         => 'string',
        'box4_weight'     => 'string',
        'box4_size'       => 'string',
        'box5_no'         => 'string',
        'box5_weight'     => 'string',
        'box5_size'       => 'string',
        'note'            => 'string',
    ];

    public static function repositoryClass(): string
    {
        return ProductRequestRepository::class;
    }

    public static function alias(): string
    {
        return 'product_request';
    }

    public static function code(): string
    {
        return 'PR';
    }
}
