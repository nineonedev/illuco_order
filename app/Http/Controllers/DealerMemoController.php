<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Domains\User\Entities\DealerMemo;
use App\Domains\User\Repositories\DealerMemoRepository;
use App\Domains\User\Repositories\UserRepository;
use App\Domains\User\Enums\UserType;
use Framework\Http\Request;
use Framework\Routing\Controller;
use Framework\Validation\Validator;
use RuntimeException;

class DealerMemoController extends Controller
{
    /**
     * GET /dealer-memo/{id}
     * - {id}: users.id (딜러 유저 ID)
     * - user -> dealer -> id 로 따라가서 dealer_memos 조회
     */
    public function edit(string $id)
    {
        // 1) 유저 + 딜러 프로필 로드
        $user = UserRepository::make()
            ->with([UserType::DEALER])   // 딜러 프로필 조인
            ->find($id);

        if (!$user) {
            throw new RuntimeException('해당 사용자가 존재하지 않습니다.');
        }

        // 2) 진짜 딜러인지 검증 (user -> dealer 존재해야 함)
        $dealer = $user->{UserType::DEALER} ?? null;
        if (!$dealer) {
            throw new RuntimeException('딜러가 아닌 사용자입니다.');
        }

        $dealerId = (int)$dealer->id; // ← user->dealer->id

        /** @var DealerMemo|null $memo */
        $memo = DealerMemoRepository::make()
            ->query()
            ->where('dealer_id', $dealerId)
            ->first();

        return $this->render('admin.pages.dealers.memo', [
            'dealer' => $user,
            'memo'   => $memo, // null 가능
        ]);
    }

    /**
     * POST /dealer-memo/{id}
     * - {id}: users.id (딜러 유저 ID)
     * - user -> dealer -> id 로 따라가서 dealer_memos 업서트
     * 바디: memo_general, memo_production, is_pinned_general(bool), is_pinned_prod(bool)
     */
    public function save(Request $request, string $id)
    {
        // 유효성 검증
        $validator = Validator::make($request->all(), [
            'memo_general'       => 'nullable',
            'memo_production'    => 'nullable',
            'is_pinned_general'  => 'nullable|boolean',
            'is_pinned_prod'     => 'nullable|boolean',
        ]);
        $validator->validateOrFail();

        $validated = $validator->validated();
        $isPinnedGeneral = (bool)($validated['is_pinned_general'] ?? false);
        $isPinnedProd    = (bool)($validated['is_pinned_prod'] ?? false);

        return $this->runInTransaction(function () use ($id, $validated, $isPinnedGeneral, $isPinnedProd) {
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

            $dealerId = (int)$dealer->id; // ← user->dealer->id

            // 2) 기존 메모 조회
            /** @var DealerMemo|null $memo */
            $memo = DealerMemoRepository::make()
                ->query()
                ->where('dealer_id', $dealerId)
                ->first();

            if ($memo) {
                // 업데이트
                $memo->memo_general       = array_key_exists('memo_general', $validated) ? $validated['memo_general'] : $memo->memo_general;
                $memo->memo_production    = array_key_exists('memo_production', $validated) ? $validated['memo_production'] : $memo->memo_production;
                $memo->is_pinned_general  = $isPinnedGeneral;
                $memo->is_pinned_prod     = $isPinnedProd;
            } else {
                // 새로 생성 (PK = dealer_id)
                $memo = new DealerMemo([
                    'dealer_id'         => $dealerId,
                    'memo_general'      => $validated['memo_general'] ?? null,
                    'memo_production'   => $validated['memo_production'] ?? null,
                    'is_pinned_general' => $isPinnedGeneral,
                    'is_pinned_prod'    => $isPinnedProd,
                ]);
            }

            if (function_exists('user') && user()) {
                $memo->updated_by = (int)user()->id;
            }

            $saved = DealerMemoRepository::make()->save($memo);
            if (!$saved) {
                throw new RuntimeException('메모 저장에 실패했습니다.');
            }

            return $this->render(null, [
                'dealer_id'         => $dealerId,
                'memo_general'      => (string)($memo->memo_general ?? ''),
                'memo_production'   => (string)($memo->memo_production ?? ''),
                'is_pinned_general' => (bool)$memo->is_pinned_general,
                'is_pinned_prod'    => (bool)$memo->is_pinned_prod,
                'updated_by'        => $memo->updated_by,
                'updated_at'        => (string)$memo->updated_at,
            ], '정상적으로 저장되었습니다.');
        });
    }
}
