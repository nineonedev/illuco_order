<?php

namespace App\Http\Controllers\Order;

use App\Domains\Order\Entities\OrderHistory;
use App\Domains\Order\Repositories\OrderHistoryRepository;
use App\Domains\Order\Repositories\OrderRepository;
use Framework\Http\Request;
use Framework\Routing\Controller;
use RuntimeException;

class OrderHistoryController extends Controller
{
    public function index(string $orderNo)
    {
        $order = OrderRepository::make()
            ->query()
            ->where('order_no', $orderNo)
            ->firstOrFail();

        $histories = OrderHistoryRepository::make()
            ->query()
            ->where('order_id', $order->id)
            ->where('settled', false) // or visible_on_orders
            ->get();

        return $this->render(null, [
            'histories' => array_map(fn($h) => $h->toArray(), $histories)
        ], '정상적으로 로드되었습니다.');
    }


    public function store(Request $request)
    {
        return $this->runInTransaction(function () use ($request) {
            $request->validateOrFail([
                'order_id'   => 'required|integer',
                'balance'    => 'nullable|number',
                'settled'    => 'nullable|boolean',
                'memo'       => 'nullable|string',
                'dealer_id'  => 'nullable|integer',
                'customer_id'=> 'nullable|integer',
            ]);

            $data = $request->safe();
            $data['dealer_id'] = $data['dealer_id'] ?: null;
            $data['customer_id'] = $data['customer_id'] ?: null;
            $data['settled'] = $data['settled'] ?? 0;

            $history = new OrderHistory($data);
            $history->created_by = user()->id;

            $history = OrderHistoryRepository::make()->save($history);

            if (!$history) {
                throw new RuntimeException("히스토리 저장에 실패하였습니다.");
            }

            return $this->render(null, [
                'history' => $history->toArray(),
            ], '히스토리가 정상적으로 등록되었습니다.');
        });
    }


    public function update(int $id, Request $request)
    {
        return $this->runInTransaction(function() use ($id, $request) {
            $history = OrderHistoryRepository::make()->findOrFail($id);
            $history->fill($request->all());

            $history = OrderHistoryRepository::make()->save($history);

            if (!$history) {
                throw new RuntimeException("히스토리 수정에 실패하였습니다.");
            }

            return $this->render(null, [
                'history' => $history->toArray(),
            ], '정상적으로 수정되었습니다.');
        });
    }

    public function destroy(int $id)
    {
        return $this->runInTransaction(function() use ($id) {
            $history = OrderHistoryRepository::make()->findOrFail($id);
            $success = OrderHistoryRepository::make()->delete($history);

            if (!$success) {
                throw new RuntimeException("히스토리 삭제에 실패하였습니다.");
            }

            return $this->render(null, [], '히스토리가 삭제되었습니다.');
        });
    }
}
