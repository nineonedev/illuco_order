<?php

namespace App\Services\Order;

use App\Domains\Order\Entities\CartItem;
use App\Domains\Order\Entities\Customer;
use App\Domains\Order\Repositories\CartItemRepository;
use App\Domains\Order\Repositories\CartRepository;
use App\Domains\Order\Repositories\CustomerRepository;
use App\Domains\Product\Entities\Product;
use App\Domains\Product\Entities\ProductValue;
use App\Domains\Product\Repositories\ProductRepository;
use App\Domains\Product\Repositories\ProductValueRepository;
use App\Supports\Services\Service;
use Exception;
use Framework\Database\ORM\Entities\Entity;
use RuntimeException;

class AddItemToCartService extends Service
{
    protected function handle(array $payload): array
    {
        // $payload = [
        //     'product' => [],
        //     'attributes' => [ id => value, id => value ],
        //     'quantity' => 2,
        //     'customer_id' => 3
        // ];

        $customer_id = $payload['customer_id'];
        $product = $payload['product'] ?? [];
        $attributes = $payload['attributes'] ?? [];
        $optionJson = json($attributes);
        $quantity = $payload['quantity'] ?? 1;

        $cart = CartRepository::make()->query()->firstOrCreate(['customer_id' => $customer_id]);
        $product = new Product(array_merge($product, ['option_json' => $optionJson]));
        $product = ProductRepository::make()->save($product);


        foreach ($attributes as $attribute_id => $value) {
            $values = is_array($value) ? $value : [$value];

            foreach ($values as $v) {
                $productValue = ProductValue::make([
                    'product_id'    => $product->id,
                    'attribute_id'  => $attribute_id,
                    'value'         => $v,
                ]);
                
                ProductValueRepository::make()->save($productValue);
            }
        }

        $cartItem = new CartItem([
            'cart_id' => $cart->id, 
            'product_id' => $product->id, 
            'quantity'=> $quantity
        ]);

        $cartItem = CartItemRepository::make()->save($cartItem);

        if (!$cartItem) {
            throw new RuntimeException("장바구니 추가에 실패하였습니다.");
        }

        $customer = CustomerRepository::make()
            ->with(['cart.cartitems.product.values'])
            ->query()
            ->find($customer_id);

        return [
            'success' => true,
            'message' => '장바구니에 추가되었습니다.',
            'data' => [
                'customer' => $customer->toArray(),
            ]
        ];
    }
}