<?php 

namespace App\Domains\Order\Entities;

use App\Domains\Order\Repositories\CartRepository;
use Framework\Database\ORM\Entities\Entity;

class Cart extends Entity
{
    protected array $fillable = [
        'customer_id',
    ];

    protected array $casts = [
        'customer_id' => 'int',
    ];

    public static function repositoryClass(): string
    {
        return CartRepository::class;
    }
}