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
    public function edit(string $id)
    {
        $user = UserRepository::make()->with([UserType::DEALER])->find($id);
        if (!$user) throw new RuntimeException('해당 사용자가 존재하지 않습니다.');
        $dealer = $user->{UserType::DEALER} ?? null;
        if (!$dealer) throw new RuntimeException('딜러가 아닌 사용자입니다.');

        $prices = DealerPriceRepository::make()
            ->with(['template.fileattachment'])
            ->query()->where('dealer_id', (int)$dealer->id)->get();

        return $this->render('admin.pages.dealers.price', [
            'dealer'  => $user,
            'prices'  => $prices,
        ]);
    }

    /** 신규 추가 (상단 폼: POST /dealer-price/{dealer}) */
    public function store(Request $request, string $id)
    {
        Validator::make($request->all(), [
            'product_template_id' => 'required|integer',
            'price'               => 'required',
            'is_active'           => 'nullable|boolean',
        ])->validateOrFail();

        return $this->runInTransaction(function () use ($request, $id) {
            $user = UserRepository::make()->with([UserType::DEALER])->find($id);
            if (!$user) throw new RuntimeException('해당 사용자가 존재하지 않습니다.');
            $dealer = $user->{UserType::DEALER} ?? null;
            if (!$dealer) throw new RuntimeException('대리점이 아닌 사용자입니다.');
            $id = (int)$dealer->id;

            $tplId    = (int)$request->body('product_template_id');
            $price    = (float)$request->body('price');
            $isActive = (int)($request->body('is_active', 0) ? 1 : 0);

            if (!$tplId){
                throw new RuntimeException("제품을 선택해주세요.");
            }

            // upsert
            $model = DealerPriceRepository::make()->query()
                ->where('dealer_id', $id)
                ->where('product_template_id', $tplId)
                ->first();

            if ($model) {
                throw new RuntimeException("이미 등록된 모델입니다.");
            }

            $model = new DealerPrice([
                'dealer_id' => $id,
                'product_template_id' => $tplId,
                'price' => $price,
                'is_active' => $isActive,
            ]);

            $saved = DealerPriceRepository::make()->save($model);

            if (!$saved) {
                throw new RuntimeException("단가 등록 중 문제가 발생하였습니다.");
            }

            return $this->render(null, ['id' => $model->id], '대리점 단가가 저장되었습니다.');
        });
    }

    /** 행 저장 (PUT /dealer-price/{price}) */
    public function update(Request $request, string $id)
    {
        // 부분 업데이트 허용
        Validator::make($request->all(), [
            'price'               => 'required',
            'is_active'           => 'boolean',
        ])->validateOrFail();

        return $this->runInTransaction(function () use ($request, $id) {
            $repo  = DealerPriceRepository::make();
            /** @var DealerPrice $model */
            $model = $repo->findOrFail($id);

            if ($request->has('price')) {
                $model->price = (float)$request->body('price');
            }
            if ($request->has('is_active')) {
                $model->is_active = (int)($request->body('is_active') ? 1 : 0);
            }

            if ($request->has('product_template_id')) {
                $model->product_template_id = (int)$request->body('product_template_id');
            }

            $repo->save($model);

            return $this->render(null, ['id' => $model->id], '저장되었습니다.');
        });
    }

    /** 행 삭제 (DELETE /dealer-price/{price}) */
    public function destroy(string $id)
    {
        return $this->runInTransaction(function () use ($id) {
            $repo  = DealerPriceRepository::make();
            $model = $repo->findOrFail($id);
            if (!$repo->delete($model)) {
                throw new RuntimeException('삭제에 실패했습니다.');
            }
            return $this->render(null, [], '삭제되었습니다.');
        });
    }
}
