<?php

namespace App\Http\Controllers\Order;

use App\Domains\Order\Repositories\CustomerRepository;
use App\Domains\User\Repositories\DealerMemoRepository;
use App\Services\Order\AddItemToCartService;
use Framework\Http\Request;
use Framework\Routing\Controller;

class CartController extends Controller
{
    public function index(Request $request)
    {
        // ✅ 어떤 대리점 메모를 보여줄지 결정
        $dealerId   = null;
        $dealerMemo = null;

        // 1) 대리점 로그인이라면 본인 대리점
        if (user()->isDealer() && user()->dealer) {
            $dealerId = (int) user()->dealer->id;
        }

        // 2) 쿼리로 대리점이 지정된 경우 우선
        if ($request->query('dealer_id')) {
            $dealerId = (int) $request->query('dealer_id');
        }

        // 3) 고객이 지정됐다면 고객의 dealer_id 사용
        elseif ($request->query('customer_id')) {
            $customer = CustomerRepository::make()->find($request->query('customer_id'));
            if ($customer && $customer->dealer_id) {
                $dealerId = (int) $customer->dealer_id;
            }
        }

        // ✅ 대리점이 결정됐으면 메모 로드(일반/생산팀 둘 다 전달, 화면에서 일반만 쓰면 됨)
        if ($dealerId) {
            $dealerMemo = DealerMemoRepository::make()
                ->query()
                ->where('dealer_id', $dealerId)
                ->first();
        }
        
        return $this->render('admin.pages.cart.index', [
            'dealer_id'  => $dealerId,
            'dealerMemo' => $dealerMemo,
        ]);
    }


    public function store(Request $request)
    {
        $result = (new AddItemToCartService())->runInTransaction($request->all());
        return $result->toResponse(); 
    }
}