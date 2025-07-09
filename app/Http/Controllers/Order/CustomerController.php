<?php

namespace App\Http\Controllers\Order;

use App\Domains\Order\Entities\CartItem;
use App\Domains\Order\Entities\Customer;
use App\Domains\Order\Repositories\CustomerRepository;
use App\Domains\User\Entities\Dealer;
use App\Domains\User\Enums\UserType;
use Framework\Http\Request;
use Framework\Routing\Controller;
use RuntimeException;

class CustomerController extends Controller
{
    protected function repo(): CustomerRepository
    {
        return CustomerRepository::make();
    }

    public function index(Request $request)
    {
        $query = $this->repo()->with([
            'user.dealer',
            'cart.cartitems.product.template' => [
                'fileattachment',
                'category',
            ],
        ])->query();

        $user = user(); 

        if ($user->isDealer()) {
            $query->where('dealer_id', $user->dealer->id);
        }

        $query->when($keyword = $request->query('q'), function ($q) use ($keyword) {
            $q->where('name', 'like', "%{$keyword}%")
            ->orWhere('email', 'like', "%{$keyword}%");
        });

        $perpage = $request->query('perpage', 15);
        $page = $request->query('page', 1);

        $customers = $query->paginate($perpage, $page);

        $customers->items()->transform(function ($customer) {
            if ($customer->cart && $customer->cart->cartitems) {
                foreach ($customer->cart->cartitems as $item) {
                    $subType = $item->product->type;

                    if ($subType) {
                        $item->product->load([$subType]);
                    }
                }


                $groupedItems = CartItem::groupBySet(
                    $customer->cart->cartitems
                );

                $customer->cart->setRelation('cartitems_grouped', $groupedItems);
            }
            return $customer;
        });

        return $this->render('admin.pages.customers.index', [
            'customers' => $request->expectsJson() ? $customers->toArray() : $customers,
            'query'     => $request->query(),
        ]);
    }


    public function show(string $id)
    {
        $customer = $this->repo()->with(['cart.cartitems'])->find($id);

        if (!$customer) {
            throw new RuntimeException("고객 정보를 찾을 수 없습니다.");
        }

        return $this->render('admin.pages.customers.edit', [
            'customer' => $customer->toArray(),
        ]);
    }

    public function create()
    {
        return $this->render('admin.pages.customers.create', [], '정상적으로 생성되었습니다.');
    }

    public function edit(string $id)
    {
        $customer = $this->repo()->find($id);
        if (!$customer) {
            throw new RuntimeException("고객 정보를 찾을 수 없습니다.");
        }

        return $this->render('admin.pages.customers.edit', [
            'customer' => $customer,
        ]);
    }

    public function store(Request $request)
    {
        return $this->runInTransaction(function () use ($request) {
            $user = user(); 
            $customer = new Customer($request->all());
            $customer->user_id = $user->id;

            if ($user->isDealer()) {
                $customer->dealer_id = $user->dealer->id;
            }

            $customer = $this->save($customer);
            return $this->render(null, ['customer' => $customer->toArray()], '성공적으로 저장되었습니다.');
        });
    }

    public function update(string $id, Request $request)
    {
        return $this->runInTransaction(function () use ($id, $request) {
            $customer = $this->repo()->findOrFail($id);
            $customer->fill($request->all());

            $customer = $this->save($customer);

            return $this->render(null, ['customer' => $customer->toArray()], '성공적으로 저장되었습니다.');
        });
    }

    public function destroy(string $id)
    {
        return $this->runInTransaction(function () use ($id) {
            $customer = $this->repo()->findOrFail($id);

            if (!$this->repo()->delete($customer)) {
                throw new RuntimeException("고객 삭제에 실패했습니다.");
            }

            return $this->render(null, [], '성공적으로 삭제되었습니다.');
        });
    }

    protected function save(Customer $customer): Customer
    {
        $customer = $this->repo()->save($customer);
        
        if (!$customer) {
            throw new RuntimeException("고객 저장에 실패했습니다.");
        }

        return $customer;
    }
}
