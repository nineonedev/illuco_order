<?php

namespace App\Services\Order;

use App\Domains\Order\Entities\CartItem;
use App\Domains\Order\Repositories\CartItemRepository;
use App\Domains\Order\Repositories\CartRepository;
use App\Domains\Product\Entities\Product;
use App\Domains\Product\Repositories\ProductRepository;
use App\Domains\Product\Repositories\ProductValueRepository;
use App\Supports\Services\Service;
use RuntimeException;

class AddItemToCartService extends Service
{
    protected function handle(array $payload): array
    {
        // 기본 변수 추출
        $customer_id = $payload['customer_id'];
        $productData = $payload['product'] ?? [];
        $attributes  = $payload['attributes'] ?? [];
        $quantity    = $payload['quantity'] ?? 1;

        // 1. 속성 배열 정규화: ['id' => ['value' => [], 'type' => 'type']] 형태로
        $normalized = [];
        foreach ($attributes as $id => $attribute) {
            // type이 없으면 'text' 기본값 설정
            $type = $attribute['type'] ?: 'text'; 
            // value가 없으면 continue로 해당 속성 건너뛰기
            $value = $attribute['value'] ?? null;

            if (!$value) continue; // value가 없으면 건너뜁니다.

            // value가 배열로 들어오는 경우를 처리
            $normalized[$id] = [
                'value' => is_array($value) ? array_values($value) : [$value],
                'type'  => $type,
            ];
        }

        // 2. 장바구니 조회 or 생성
        $cart = CartRepository::make()
            ->query()
            ->firstOrCreate(['customer_id' => $customer_id]);

        // 3. 제품 생성 (속성 포함)
        $product = new Product(array_merge($productData, ['attribute_json' => $normalized]));
        $product = ProductRepository::make()->save($product);

        // 5. ProductValue 저장 (속성 타입 포함)
        foreach ($normalized as $attribute_id => $data) {
            $type = $data['type']; // 속성 타입 (ex: text, multi-select)

            ProductValueRepository::make()->query()
                ->where('product_id', $product->id)
                ->where('attribute_id', $attribute_id)
                ->where('attribute_type', $type)
                ->delete();

            foreach ($data['value'] as $value) {
                // ProductValue 저장
                ProductValueRepository::make()->query()->firstOrCreate([
                    'product_id'     => $product->id,
                    'attribute_id'   => $attribute_id,
                    'attribute_type' => $type,  // 타입 저장
                    'value'          => $value,
                ]);
            }
        }

        // 6. 장바구니 아이템 생성
        $cartItem = new CartItem([
            'cart_id'        => $cart->id,
            'product_id'     => $product->id,
            'quantity'       => $quantity,
        ]);

        $cartItem = CartItemRepository::make()->save($cartItem);

        if (!$cartItem) {
            logger()->error("장바구니 추가 실패", [
                'cart_id'     => $cart->id,
                'product_id'  => $product->id,
                'attributes'  => $normalized,
                'quantity'    => $quantity,
            ]);
            throw new RuntimeException("장바구니 추가에 실패하였습니다.");
        }

        // 7. 관련 정보 로딩
        $cartItem->load([
            'product.values',
            'product.template' => [
                'attributes.options',
                'fileattachment',
            ],
        ]);

        return [
            'message' => '장바구니에 추가되었습니다.',
            'data' => [
                'cartitem' => $cartItem->toArray(),
            ],
        ];
    }

}
