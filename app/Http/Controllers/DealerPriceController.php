<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Domains\User\Enums\UserType;
use App\Domains\User\Repositories\UserRepository;
use App\Domains\User\Entities\DealerPrice;
use App\Domains\User\Repositories\DealerPriceRepository;
use App\Domains\Product\Repositories\ProductTemplateRepository;
use Framework\Http\Request;
use Framework\Routing\Controller;
use Framework\Validation\Validator;
use RuntimeException;

class DealerPriceController extends Controller
{
    /**
     * GET /dealer-price/{id}
     * - {id}: users.id (딜러 유저 ID)
     * - user -> dealer -> id 로 따라가서 해당 딜러의 개별 제품 단가 목록 조회
     */
    public function edit(string $id)
    {
        // 1) 유저 + 딜러 프로필 로드
        $user = UserRepository::make()
            ->with([UserType::DEALER])
            ->find($id);

        if (!$user) {
            throw new RuntimeException('해당 사용자가 존재하지 않습니다.');
        }

        $dealer = $user->{UserType::DEALER} ?? null;
        if (!$dealer) {
            throw new RuntimeException('딜러가 아닌 사용자입니다.');
        }

        $dealerId = (int) $dealer->id;

        // 2) 제품 템플릿 전체 + 해당 딜러의 가격 맵
        $templates = ProductTemplateRepository::make()
            ->query()
            ->orderBy('sort_order', 'DESC')
            ->orderBy('created_at', 'DESC')
            ->get();

        $prices = DealerPriceRepository::make()
            ->with(['template.fileattachment'])
            ->query()
            ->where('dealer_id', $dealerId)
            ->get();

        return $this->render('admin.pages.dealers.price', [
            'dealer'    => $user,
            'templates' => $templates,
            'prices'  => $prices,
        ]);
    }

    /**
     * POST /dealer-price/{id}
     * - {id}: users.id (딜러 유저 ID)
     *
     * 바디(두 형태 모두 지원):
     *  1) 일괄 저장:
     *     items: [
     *       { product_template_id: int, price: number, is_active: bool },
     *       ...
     *     ]
     *  2) 단일 저장:
     *     product_template_id: int
     *     price: number
     *     is_active: bool
     */
    public function save(Request $request, string $id)
    {
        // 기본 스키마(배열 or 단일)
        $isBulk = is_array($request->body('items'));

        $rules = $isBulk
            ? [
                'items'                       => 'required|array',
                'items.*.product_template_id' => 'required|integer',
                'items.*.price'               => 'required|min:0',
                'items.*.is_active'           => 'nullable|boolean',
            ]
            : [
                'product_template_id' => 'required|integer',
                'price'               => 'required|min:0',
                'is_active'           => 'nullable|boolean',
            ];

        Validator::make($request->all(), $rules)->validateOrFail();

        return $this->runInTransaction(function () use ($request, $id, $isBulk) {
            // 1) 유저 + 딜러 프로필 로드
            $user = UserRepository::make()
                ->with([UserType::DEALER])
                ->find($id);

            if (!$user) {
                throw new RuntimeException('해당 사용자가 존재하지 않습니다.');
            }

            $dealer = $user->{UserType::DEALER} ?? null;
            if (!$dealer) {
                throw new RuntimeException('딜러가 아닌 사용자입니다.');
            }

            $dealerId = (int) $dealer->id;

            // 업서트 헬퍼
            $upsert = function (int $templateId, $price, bool $isActive) use ($dealerId) {
                /** @var DealerPrice|null $model */
                $model = DealerPriceRepository::make()
                    ->query()
                    ->where('dealer_id', $dealerId)
                    ->where('product_template_id', $templateId)
                    ->first();

                if ($model) {
                    $model->price     = $price;
                    $model->is_active = $isActive;
                } else {
                    $model = new DealerPrice([
                        'dealer_id'           => $dealerId,
                        'product_template_id' => $templateId,
                        'price'               => $price,
                        'is_active'           => $isActive,
                    ]);
                }

                $saved = DealerPriceRepository::make()->save($model);
                if (!$saved) {
                    throw new RuntimeException("가격 저장에 실패했습니다. (template_id: {$templateId})");
                }

                return $model;
            };

            $savedRows = [];

            if ($isBulk) {
                foreach ((array)$request->body('items') as $row) {
                    $templateId = (int)$row['product_template_id'];
                    $price      = (float)$row['price'];
                    $isActive   = (bool)($row['is_active'] ?? false);

                    $savedRows[] = $upsert($templateId, $price, $isActive)->toArray();
                }
            } else {
                $templateId = (int)$request->body('product_template_id');
                $price      = (float)$request->body('price');
                $isActive   = (bool)$request->body('is_active', false);

                $savedRows[] = $upsert($templateId, $price, $isActive)->toArray();
            }

            return $this->render(null, [
                'dealer_id' => $dealerId,
                'saved'     => $savedRows,
            ], '정상적으로 저장되었습니다.');
        });
    }
}
