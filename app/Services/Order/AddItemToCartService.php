<?php

namespace App\Services\Order;

use App\Domains\Order\Entities\CartItem;
use App\Domains\Order\Repositories\CartItemRepository;
use App\Domains\Order\Repositories\CartRepository;
use App\Domains\Product\Entities\Headlight;
use App\Domains\Product\Entities\Loupe;
use App\Domains\Product\Entities\Product;
use App\Domains\Product\Entities\ProductValue;
use App\Domains\Product\Repositories\LoupeRepository;
use App\Domains\Product\Repositories\ProductRepository;
use App\Domains\Product\Repositories\ProductValueRepository;
use App\Supports\Services\Service;
use Exception;
use Framework\Database\ORM\Entities\Entity;
use Framework\Database\ORM\Repositories\Repository;
use Framework\Support\Str;
use RuntimeException;

class AddItemToCartService extends Service
{
    protected function handle(array $payload): array
    {
        // 기본 변수 추출
        $customer_id = $payload['customer_id'];
        $productData = $payload['product'] ?? [];
        $quantity    = $payload['quantity'] ?? 1;
        $sets = $payload['sets'] ?? [];

        // 카트 가져오기
        $cart = CartRepository::make()
            ->query()
            ->firstOrCreate(['customer_id' => $customer_id]);


        // 제품 생성
        $type = $productData['type'] ?? null; 
        $product = new Product(array_merge($productData, [
            'template_id' => $productData['template_id'],
            'name' => $productData['name'],
            'type' => $productData['type'],
            'code' => $productData['code'],
            'model' => $productData['model'],
            'price' => $productData['price'],
            'description' => $productData['description'] ?? null,
        ]));
        
        $product = ProductRepository::make()->save($product);

        if (!$product) {
            throw new RuntimeException("제품 생성에 실패하였습니다."); 
        }

        // 서브 제품 생성
        
        if ($type) {
            /** @var Entity|null $subProductClass */
            $subProductClass = null;
            $subProductData = $payload[$type];

            switch ($type) {
                case Loupe::alias():
                    $subProductClass = Loupe::class; 

                    $useEngraving = $subProductData['use_engraving'] ?? false;
                    
                    if (!$useEngraving) {
                        $subProductData['engraving_text'] = null;
                    } else {
                        $quantity = 1; 
                    }

                    break; 
                case Headlight::alias():
                    $subProductClass = Headlight::class;

                    $useEngraving = $subProductData['use_engraving'] ?? false;
                    
                    if (!$useEngraving) {
                        $subProductData['engraving_text'] = null;
                    } else {
                        $quantity = 1; 
                    }

                    break; 
            }

            if ($subProductClass) {
                
                if (!isset($payload[$type])) {
                    throw new RuntimeException("Payload에 alias key '{$type}' 가 없습니다.");
                }
                
                $subProductData = array_merge($subProductData, ['id' => $product->id]);
                $subProductEntity = new $subProductClass($subProductData);
                
                /** @var Repository $repo */
                $repo = $subProductClass::repositoryClass()::make();

                $subProduct = $repo->save($subProductEntity);

                if (!$subProduct) {
                    throw new RuntimeException("제품 확장에 실패하였습니다.");
                }

                $product->setRelation($type, $subProduct);
            }
        }

        $setGroupId = $sets ? Str::uuid() : null; 

        // 카트 아이템 생성
        $cartItemData = array_merge([
            'cart_id'        => $cart->id,
            'product_id'     => $product->id,
            'quantity'       => $quantity,
        ], [
            'set_group_id' => $setGroupId,
            'is_main_item' => true,
        ]);
        
        $cartItem = new CartItem($cartItemData);
        $cartItem = CartItemRepository::make()->save($cartItem);
        
        if (!$cartItem) {
            logger()->error("장바구니 추가 실패", [
                'cart_id'     => $cart->id,
                'product_id'  => $product->id,
                'quantity'    => $quantity,
            ]);

            throw new RuntimeException("장바구니 추가에 실패하였습니다.");
        }

        // 카트아이템 세트 생성
        if ($sets) { 
            $setGroupProducts = []; 

            foreach ($sets as $index => $data) {
                $setGroupProduct = new Product($data['product'] ?? []); 
                $setGroupProduct = ProductRepository::make()->save($setGroupProduct);
                $quantity = $data['quantity'] ?? 1;
                $setGroupProduct->load(['template.fileattachment']);

                if (!$setGroupProduct) {
                    throw new RuntimeException("세트 생성에 실패하였습니다.");
                }

                $subCartItemData = [
                    'cart_id' => $cart->id,
                    'product_id' => $setGroupProduct->id,
                    'quantity' => $quantity,
                    'is_main_item' => false, 
                    'set_group_id' => $setGroupId, 
                    'set_group_sort' => $index,
                ];
                
                $subCartItem = new CartItem($subCartItemData);
                $subCartItem = CartItemRepository::make()->save($subCartItem);

                if (!$subCartItem) {
                    throw new RuntimeException("장바구니 세트 추가에 실패하였습니다.");
                }

                $subCartItem->setRelation('product', $setGroupProduct);
                $setGroupProducts[] = $subCartItem;
            }

            usort($setGroupProducts, function ($a, $b) {
                return ($a->set_group_sort ?? 0) <=> ($b->set_group_sort ?? 0);
            });

            $cartItem->setRelation('sets', $setGroupProducts);
        }

        $cartItem->load([
            'product.template' => [
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
