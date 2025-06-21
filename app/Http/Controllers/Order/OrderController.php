<?php

namespace App\Http\Controllers\Order;

use App\Domains\Order\Entities\Order;
use App\Domains\Order\Entities\OrderItem;
use App\Domains\Order\Repositories\CartItemRepository;
use App\Domains\Order\Repositories\CustomerRepository;
use App\Domains\Order\Repositories\OrderItemRepository;
use App\Domains\Order\Repositories\OrderRepository;
use App\Domains\User\Entities\User;
use Exception;
use Framework\Http\Request;
use Framework\Routing\Controller;
use RuntimeException;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = OrderRepository::make()->with(['customer'])->query();

        $query->when($s = $request->query('status'), fn($q) => $q->where('order_status', $s))
            ->when($n = $request->query('name'), fn($q) => 
                $q->where('orderer_name', 'like', "%{$n}%")
                    ->orWhereHas('customer', fn($cq) => $cq->where('name', 'like', "%{$n}%"))
            )
            ->when($d = $request->query('dealer'), fn($q) => 
                $q->whereHas('customer', fn($cq) => $cq->where('dealer_id', $d))
            )
            ->orderByDesc('id');

        return $this->render('admin.pages.orders.index', [
            'orders' => $query->paginate($request->query('perpage', 15), $request->query('page', 1)),
            'query'  => $request->query(),
        ]);
    }


    public function show()
    {

    }

    public function create()
    {

    }

    public function store(Request $request)
{
    return $this->runInTransaction(function () use ($request) {
        $ids = $request->body('ids', []);

        if (is_string($ids)) {
            $ids = explode(',', $ids);
        }

        if (empty($ids)) {
            throw new RuntimeException("주문할 장바구니 항목이 없습니다.");
        }

        $customer_id = $request->body('customer_id');
        $memo = $request->body('memo', '');

        $customer = CustomerRepository::make()->findOrFail($customer_id);
        $cartitems = CartItemRepository::make()
            ->with(['product'])
            ->query()
            ->findMany($ids);

        if (empty($cartitems)) {
            throw new RuntimeException("선택한 장바구니 항목을 찾을 수 없습니다.");
        }

        $totalAmount = array_reduce($cartitems, function ($acc, $item) {
            return $acc + ($item->product->price * $item->quantity);
        }, 0);

        $user = user();
        $user->load([User::morphType()]);

        $phoneNumber = $user->{User::morphType()}->phone_number ?? null;

        $orderData = [
            'customer_id'    => $customer->id,
            'user_id'        => $user->id,
            'orderer_name'   => $user->name,
            'orderer_email'  => $user->email,
            'orderer_phone'  => $phoneNumber,
            'memo'           => $memo,
            'total_amount'   => $totalAmount,
            'order_status'   => Order::STATUS_RECEIVED,
        ];

        $order = new Order($orderData);
        $order = OrderRepository::make()->save($order);

        // OrderItem 엔티티 저장
        foreach ($cartitems as $cartitem) {
            $orderItem = new OrderItem([
                'order_id'   => $order->id,
                'product_id' => $cartitem->product_id,
                'quantity'   => $cartitem->quantity,
                'price'      => $cartitem->product->price,
            ]);

            OrderItemRepository::make()->save($orderItem);
        }

        // 장바구니 항목 제거
        CartItemRepository::make()->query()->bulkDelete($ids);

        return $this->render(null, ['order' => $order->toArray()], '주문이 생성되었습니다.');
    });
}


    public function edit()
    {

    }

    public function update()
    {

    }

    public function destroy()
    {

    }
}