<?php

namespace App\Http\Controllers\Order;

use App\Domains\Order\Entities\CartItem;
use App\Domains\Order\Entities\Customer;
use App\Domains\Order\Repositories\CustomerRepository;
use App\Domains\User\Entities\Dealer;
use App\Domains\User\Enums\UserType;
use App\Domains\User\Repositories\DealerRepository;
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

        // 대리점 로그인 시 자기 고객만 조회
        if ($user->isDealer()) {
            $query->where('dealer_id', $user->dealer->id);
        } else {
            $query->when(
                $dealerId = $request->query('dealer_id'),
                fn($q) => $q->where('dealer_id', $dealerId)
            );
        }

        // 국가
        $query->when(
            $country = $request->query('country'),
            fn($q) => $q->where('country', $country)
        );

        // 이름
        $query->when(
            $name = $request->query('name'),
            fn($q) => $q->where('name', 'like', "%{$name}%")
        );

        // 이메일
        $query->when(
            $email = $request->query('email'),
            fn($q) => $q->where('email', 'like', "%{$email}%")
        );

        // 전화번호
        $query->when(
            $phone = $request->query('phone'),
            fn($q) => $q->where('phone', 'like', "%{$phone}%")
        );


        $query->when(
            $search = $request->query('search'),
            fn($q) => $q->where(function ($qq) use ($search) {
                $qq->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
                ->orWhere('phone', 'like', "%{$search}%");
            })
        );

        // 정렬
        $sort = $request->query('sort');
        if ($sort) {
            switch ($sort) {
                case 'latest':
                    $query->orderByDesc('created_at');
                    break;
                case 'oldest':
                    $query->orderBy('created_at');
                    break;
                case 'created_at_asc':
                    $query->orderBy('created_at');
                    break;
                case 'created_at_desc':
                    $query->orderByDesc('created_at');
                    break;
                case 'name_asc':
                    $query->orderBy('name');
                    break;
                case 'name_desc':
                    $query->orderByDesc('name');
                    break;
                case 'email_asc':
                    $query->orderBy('email');
                    break;
                case 'email_desc':
                    $query->orderByDesc('email');
                    break;
                default:
                    $query->orderByDesc('created_at');
                    break;
            }
        } else {
            $query->orderByDesc('created_at');
        }

        $perpage = $request->query('perpage', 15);
        $page = $request->query('page', 1);

        $customers = $query->paginate($perpage, $page);

        // 그룹핑 로직 유지
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

        if (user()->isDealer()) {
            $dealers = [];
        } else {
            $dealers = DealerRepository::make()
                ->withoutTrashed()    
                ->with(['user'])
                ->all();
                
            $dealers = array_values(array_filter($dealers, fn($dealer) => $dealer->user));
        }

        return $this->render('admin.pages.customers.index', [
            'customers' => $request->expectsJson() ? $customers->toArray() : $customers,
            'query'     => $request->query(),
            'countries' => __('system.countries'),
            'dealers' => request()->expectsJson() ? array_map(fn($d) => $d->toArray(), $dealers) : $dealers,
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

    public function destroyMany(Request $request)
    {
        $ids = $request->body('ids', []);

        if (is_string($ids)) {
            $ids = explode(',', $ids);
        }

        if (empty($ids)) {
            return $this->render(null, [], "삭제할 고객이 없습니다.");
        }

        return $this->runInTransaction(function () use ($ids) {
            $totalDeleted = 0;

            foreach ($ids as $id) {
                $customer = $this->repo()->find($id);
                if (!$customer) continue;

                $deleted = $this->repo()->delete($customer);
                if ($deleted) {
                    $totalDeleted++;
                }
            }

            return $this->render(null, [], "선택된 고객이 삭제되었습니다. (삭제된 수: {$totalDeleted})");
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
