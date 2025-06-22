<?php 

namespace App\Domains\Order\Entities;

use App\Domains\Order\Repositories\CartItemRepository;
use Framework\Database\ORM\Entities\Entity;

class CartItem extends Entity
{
    protected array $fillable = [
        'cart_id',
        'product_id',
        'quantity',
    ];

    protected array $casts = [
        'cart_id' => 'int',
        'product_id' => 'int',
        'quantity' => 'int',
    ];

    public static function repositoryClass(): string
    {
        return CartItemRepository::class;
    }

    public function getAttributeJson(): array
    {
        return $this->product->attribute_json ?? [];
    }
}
