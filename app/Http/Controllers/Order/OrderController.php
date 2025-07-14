<?php

namespace App\Http\Controllers\Order;

use App\Domains\Order\Entities\CartItem;
use App\Domains\Order\Entities\Order;
use App\Domains\Order\Entities\OrderItem;
use App\Domains\Order\Enums\OrderStatus;
use App\Domains\Order\Repositories\CartItemRepository;
use App\Domains\Order\Repositories\CartRepository;
use App\Domains\Order\Repositories\CustomerRepository;
use App\Domains\Order\Repositories\OrderDocumentRepository;
use App\Domains\Order\Repositories\OrderHistoryRepository;
use App\Domains\Order\Repositories\OrderItemRepository;
use App\Domains\Order\Repositories\OrderLogRepository;
use App\Domains\Order\Repositories\OrderRepository;
use App\Domains\Product\Entities\Loupe;
use App\Domains\Product\Entities\Product;
use App\Domains\Product\Repositories\CategoryRepository;
use App\Domains\Product\Repositories\ProductRepository;
use App\Domains\User\Entities\User;
use App\Domains\User\Enums\UserType;
use App\Domains\User\Repositories\DealerRepository;
use App\Domains\User\Repositories\UserRepository;
use Framework\Http\Request;
use Framework\Http\Response;
use Framework\Routing\Controller;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use RuntimeException;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = OrderRepository::make()->with([
            'customer', 
            'user', 
            'items.product' => [
                'template' => [
                    'fileattachment',
                    'category'
                ],
                'loupe',
                'headlight',
            ] 
        ])->query();

        // 대리점인 경우, 대리점에 해당하는 주문만 조회
        if (user()->isDealer()) {
            $dealerId = user()->dealer->id; // 대리점의 ID
            $query->whereHas('customer', function($q) use ($dealerId) {
                $q->where('dealer_id', $dealerId); // 고객의 dealer_id 필터링
            });
        }

        // ✅ 상태
        if ($status = $request->query('status')) {
            $query->where('order_status', $status);
        }

        // ✅ 카테고리
        if ($categoryId = $request->query('category')) {
            $query->whereHas('items.product.template', function($q) use ($categoryId) {
                $q->where('category_id', $categoryId);
            });
        }

        // ✅ 국가
        if ($country = $request->query('country')) {
            $query->where(function($q) use ($country) {
                $q->whereHas('customer', function($cq) use ($country) {
                    $cq->where('country', $country);
                })
                ->orWhereHas('user.dealer', function($uq) use ($country) {
                    $uq->where('country', $country);
                });
            });
        }

        // ✅ 주문자 (이름)
        if ($name = $request->query('name')) {
            $query->where(function($q) use ($name) {
                $q->where('orderer_name', 'like', "%{$name}%")
                ->orWhereHas('customer', function($cq) use ($name) {
                    $cq->where('name', 'like', "%{$name}%");
                });
            });
        }

        // ✅ 대리점
        if ($dealerId = $request->query('dealer')) {
            $query->whereHas('customer', function($cq) use ($dealerId) {
                $cq->where('dealer_id', $dealerId);
            });
        }

        // ✅ 검색어 (query) - 이름, 이메일, 전화번호, 연락처 등
        if ($keyword = $request->query('query')) {
            $query->where(function($q) use ($keyword) {
                $q->where('orderer_name', 'like', "%{$keyword}%")
                ->orWhere('orderer_email', 'like', "%{$keyword}%")
                ->orWhere('orderer_phone', 'like', "%{$keyword}%")
                ->orWhereHas('customer', function($cq) use ($keyword) {
                    $cq->where('name', 'like', "%{$keyword}%");
                });
            });
        }

        // ✅ 정렬
        $sort = $request->query('sort');

        switch ($sort) {
            case 'created_at_desc':
                $query->orderByDesc('created_at');
                break;
            case 'created_at_asc':
                $query->orderBy('created_at');
                break;
            case 'name_asc':
                $query->orderBy('orderer_name');
                break;
            case 'name_desc':
                $query->orderByDesc('orderer_name');
                break;
            default:
                $query->orderByDesc('id');
                break;
        }

        $orders = $query->paginate(
            $request->query('perpage', 15), 
            $request->query('page', 1)
        );

        foreach ($orders->items() as $order) {
            $updatedItems = OrderItem::groupBySet($order->items);
            $order->setRelation('set_group_items', $updatedItems);  
        }


        $histories = OrderHistoryRepository::make()->all(); 

        return $this->render('admin.pages.orders.index', [
            'orders' => $orders,
            'query'  => $request->query(),
            'categories' => CategoryRepository::make()->all(),
            'dealers' => UserRepository::make()
                ->query()
                ->with(['dealer'])
                ->where('type', UserType::DEALER)->get(),
            'statuses' => OrderStatus::all(),
            'countries' => __('system.countries'),
        ]);
    }

    
    public function update(Request $request, int $id)
    {
        return $this->runInTransaction(function () use ($request, $id) {
            $order = OrderRepository::make()->findOrFail($id);

            $request->validateOrFail([
                'order_status' => 'required|string',
                'payment_date' => 'nullable|date',
                'delivery_date' => 'nullable|date',
                'shipping_date' => 'nullable|date',
            ]);

            $data = $request->safe();
            $newStatus = $data['order_status'];

            unset($data['order_status']);

            $order->fill($data);
            $order = OrderRepository::make()->save($order);
            $order->setStatus($newStatus);

            return $this->render(null, [
                'order' => $order->toArray(),
            ], '주문이 수정되었습니다.');
        });
    }

    public function destroy(Request $request, int $id)
    {
        return $this->runInTransaction(function () use ($id) {
            $order = OrderRepository::make()
                ->with(['items.product'])
                ->findOrFail($id);

            foreach ($order->items as $item) {
                if ($item->product) {
                    $product = $item->product;

                    switch ($order->order_status) {
                        case OrderStatus::NEW:
                        case OrderStatus::CONFIRMED:
                            ProductRepository::make()->forceDelete($product);
                            break;
                        case OrderStatus::PREPARING:
                        case OrderStatus::SHIPPED:
                            ProductRepository::make()->softDelete($product);
                            break;
                        default:
                            ProductRepository::make()->softDelete($product);
                            break;
                    }
                }

                OrderItemRepository::make()->delete($item);
            }

            $deleted = OrderRepository::make()->delete($order);

            if (!$deleted) {
                throw new RuntimeException("주문 삭제에 실패했습니다.");
            }

            return $this->responseWith()
                ->redirectRoute('admin.orders.index')
                ->message('주문이 삭제되었습니다.')
                ->withQuery()
                ->send();
        });
    }

    public function cancel(string $orderNo)
    {
        $order = OrderRepository::make()
            ->query()
            ->where('order_no', $orderNo)
            ->firstOrFail();
        
        if ($order->isFinalized()) {
            throw new RuntimeException(
                "해당 주문은 '" 
                . __('system.order.status.' . $order->order_status) 
                . "' 상태로 진행 중이어서 취소할 수 없습니다. 클레임으로 문의해 주세요."
            );
        }

        $order->setStatus(OrderStatus::CANCELED);
        $order = OrderRepository::make()->save($order);

        // 오더 취소요청 => 메일!

        if (!$order) {
            throw new RuntimeException("주문 취소에 실패하였습니다. 잠시 후에 다시 시도해주세요.");
        }

        return $this->render(null, [], '주문이 성공적으로 취소되었습니다.');
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

            // 나중에 대리점에서 주문했을때도 고려해야함. 
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

            
            $allSetIds = [];
            
            foreach ($cartitems as $cartitem) {
                $sets = CartItemRepository::make()
                    ->query()
                    ->with(['product'])
                    ->where('is_main_item', false)
                    ->where('set_group_id', $cartitem->set_group_id)
                    ->get();  

                $cartitem->setRelation('sets', $sets);

                if (!empty($cartitem->sets)) {
                    foreach ($sets as $set) {
                        $allSetIds[] = $set->id; 
                    }
                }
            }

            $totalAmount = array_reduce($cartitems, function ($acc, $item) {
                $mainPrice = $item->product->price * $item->quantity;

                $setTotal = 0;
                $qty = $item->quantity;

                if (!empty($item->sets)) {
                    foreach ($item->sets as $setGroupItem) {
                        $setTotal += $setGroupItem->product->price * $setGroupItem->quantity;
                    }
                    $setTotal *= $qty;
                }

                return $acc + $mainPrice + $setTotal;
            }, 0);

            $user = user();

            $dealer = null;

            if ($user->isDealer()) {
                $dealer = $user->dealer;
            }

            $orderData = [
                'customer_id'    => $customer->id,
                'dealer_id'      => $dealer ? $dealer->id : null,
                'user_id'        => $user->id,
                'orderer_name'   => $user->name,
                'orderer_email'  => $user->email,
                'orderer_phone'  => $user->phone,
                'memo'           => $memo,
                'total_amount'   => $totalAmount,
            ];

            $order = new Order($orderData);
            $order->generateOrderNumber($dealer ? $dealer->code : null);
            $order = OrderRepository::make()->save($order);
            // $order->sendEmailToEmployee();

            if (!$order) {
                throw new RuntimeException('주문 생성에 실패하였습니다.');
            }

            // order_items 저장
            foreach ($cartitems as $cartitem) {
                // 메인 아이템 저장
                $orderItemData = [
                    'order_id'        => $order->id,
                    'product_id'      => $cartitem->product_id,
                    'quantity'        => $cartitem->quantity,
                    'unit_price'      => $cartitem->product->price,
                    'total_price'     => $cartitem->product->price * $cartitem->quantity,
                    'set_group_id'    => $cartitem->set_group_id,
                    'set_group_sort'  => $cartitem->set_group_sort,
                    'is_main_item'    => $cartitem->is_main_item ? 1 : 0,
                ];

                $orderItem = new OrderItem($orderItemData);
                $orderItem = OrderItemRepository::make()->save($orderItem);

                if (!$orderItem) {
                    throw new RuntimeException("제품 주문에 실패하였습니다.");
                }

                // 세트 아이템도 저장
                if (!empty($cartitem->sets)) {
                    foreach ($cartitem->sets as $setGroupItem) {
                        $orderSetItemData = [
                            'order_id'        => $order->id,
                            'product_id'      => $setGroupItem->product_id,
                            'quantity'        => $setGroupItem->quantity * $cartitem->quantity,
                            'unit_price'      => $setGroupItem->product->price,
                            'total_price'     => $setGroupItem->product->price * $setGroupItem->quantity * $cartitem->quantity,
                            'set_group_id'    => $setGroupItem->set_group_id,
                            'set_group_sort'  => $setGroupItem->set_group_sort,
                            'is_main_item'    => 0,
                        ];

                        $orderSetItem = new OrderItem($orderSetItemData);
                        $orderSetItem = OrderItemRepository::make()->save($orderSetItem);

                        if (!$orderSetItem) {
                            throw new RuntimeException("세트 제품 주문에 실패하였습니다.");
                        }
                    }
                }
            }

            if (!empty($allSetIds)) {
                CartItemRepository::make()->query()->bulkDelete($allSetIds);
            }

            // 장바구니 아이템 삭제
            CartItemRepository::make()->query()->bulkDelete($ids);
            
            $documents = OrderDocumentRepository::make()->createDocuments($order);
            $order->setRelation('documents', $documents);

            return $this->render(null, [
                'order' => $order->toArray(),
            ], '주문이 생성되었습니다.');
        });
    }

    public function export(Request $request)
    {
        // ===== 데이터 준비 =====
        $query = OrderRepository::make()->with([
            'customer',
            'user',
            'items.product' => [
                'template' => [
                    'fileattachment',
                    'category'
                ],
                'loupe',
                'headlight',
            ]
        ])->query();

        if (user()->isDealer()) {
            $dealerId = user()->dealer->id;
            $query->whereHas('customer', function($q) use ($dealerId) {
                $q->where('dealer_id', $dealerId);
            });
        }

        $query->orderByDesc('id');

        $orders = $query->get();

        foreach ($orders as $order) {
            $updatedItems = OrderItem::groupBySet($order->items);
            $order->setRelation('set_group_items', $updatedItems);
        }

        // ===== 스프레드시트 =====
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // ===== 다중행 헤더 작성 =====

        /**
         * ┌────────────┬──────────────┬─────────┐
         * │ 오더 기본 정보           │ 헤드라이트 정보 │ 루페 정보 │
         * └────────────┴──────────────┴─────────┘
         */
        $sheet->mergeCells('A1:O1');
        $sheet->setCellValue('A1', '오더 기본 정보');
        $sheet->mergeCells('P1:P1');
        $sheet->setCellValue('P1', '헤드라이트 정보');
        $sheet->mergeCells('Q1:AH1');
        $sheet->setCellValue('Q1', '루페 정보');

        $sheet->getStyle('A1:AH1')->applyFromArray([
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'FFFACD'],
            ],
            'font' => [
                'bold' => true,
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // ==== 2번째 헤더 (영문 헤더) ====
        $headerRow2 = [
            'Order Number (LOT)',
            'Name',
            'Status',
            'Age',
            'Country',
            'Payment Date',
            'Delivery',
            'Shipping Date',
            'Distributor',
            'Engraving',
            'Category',
            'Serial Number',
            'Model Number',
            'Type',
            'Color for Wireless',
            'OD S',
            'OD C',
            'OD A',
            'OD ADD',
            'OS S',
            'OS C',
            'OS A',
            'OS ADD',
            'ADD Option',
            'Quantity of prescription lens',
            'Spectacle',
            'Flip-up Color',
            'WD',
            'PD Right',
            'PD Left',
            'Deviation',
            'PD Total',
            'VD',
            'Memo'
        ];

        $colIndex = 1;
        foreach ($headerRow2 as $text) {
            $sheet->setCellValueByColumnAndRow($colIndex++, 2, $text);
        }

        $sheet->getStyle('A2:AH2')->applyFromArray([
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'D9EAD3'],
            ],
            'font' => [
                'bold' => true,
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // ==== 3번째 헤더 (한글 헤더) ====
        $headerRow3 = [
            '오더번호 (LOT)',
            '이름',
            '상태',
            '나이',
            '국가',
            '발주일',
            '납기일',
            '출하일',
            '대리점',
            '각인',
            '구분',
            '시리얼 번호',
            '모델',
            '형태',
            '무선 컬러',
            'S',
            'C',
            'A',
            'ADD',
            'S',
            'C',
            'A',
            'ADD',
            'ADD 옵션',
            '처방렌즈',
            '안경테',
            'Flip-up 색상',
            'WD',
            '원거리 PD R',
            '원거리 PD L',
            '편차 (Deviation)',
            '원거리 PD 합계',
            'VD',
            '메모'
        ];

        $colIndex = 1;
        foreach ($headerRow3 as $text) {
            $sheet->setCellValueByColumnAndRow($colIndex++, 3, $text);
        }

        $sheet->getStyle('A3:AH3')->applyFromArray([
            'font' => [
                'bold' => true,
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // ===== 데이터 =====
        $rowIndex = 4;

        foreach ($orders as $order) {
            $country = $order->user && $order->user->isDealer()
                ? ($order->user->dealer ? $order->user->dealer->country : null)
                : ($order->customer ? $order->customer->country : null);

            foreach ($order->set_group_items as $item) {
                $product = $item->product;
                $category = $product && $product->template && $product->template->category
                    ? $product->template->category->label
                    : '-';
                $model = $product && $product->template
                    ? $product->template->model
                    : '-';
                $isHeadlight = $product && $product->type === 'headlight';
                $isLoupe = $product && $product->type === 'loupe';
                $headlight = $isHeadlight ? ($product->headlight ?: null) : null;
                $loupe = $isLoupe ? ($product->loupe ?: null) : null;

                $type = $headlight ? $headlight->type
                    : ($loupe ? $loupe->type : '-');

                $pd_right = $loupe ? floatval($loupe->pd_right ?? 0) : 0;
                $pd_left = $loupe ? floatval($loupe->pd_left ?? 0) : 0;
                $deviation = ($loupe && ($loupe->pd_right !== null && $loupe->pd_left !== null))
                    ? abs($pd_right - $pd_left)
                    : '-';

                $rowData = [
                    $order->order_no,
                    $order->orderer_name,
                    __('system.order.status.' . $order->order_status),
                    $order->customer ? $order->customer->age : null,
                    $country,
                    $order->payment_date,
                    $order->delivery_date,
                    $order->shipping_date,
                    $order->user ? $order->user->name : null,
                    $headlight ? $headlight->engraving_text : ($loupe ? $loupe->engraving_text : '-'),
                    $category,
                    $product ? $product->serial_number : '-',
                    $model,
                    $type,
                    $headlight ? $headlight->wireless_color : '-',
                    $loupe ? $loupe->od_sph : '-',
                    $loupe ? $loupe->od_cyl : '-',
                    $loupe ? $loupe->od_axis : '-',
                    $loupe ? $loupe->od_add : '-',
                    $loupe ? $loupe->os_sph : '-',
                    $loupe ? $loupe->os_cyl : '-',
                    $loupe ? $loupe->os_axis : '-',
                    $loupe ? $loupe->os_add : '-',
                    $loupe ? (Loupe::LABELS["add_option_" . $loupe->add_option] ?? '-') : '-',
                    $item->sets ? count($item->sets) : 0,
                    $loupe ? $loupe->frame_type : '-',
                    '-', // Flip-up Color
                    $loupe ? $loupe->working_distance : '-',
                    $pd_right,
                    $pd_left,
                    $deviation,
                    $loupe ? $loupe->pd_total : '-',
                    $loupe ? $loupe->vertex_distance : '-',
                    $order->memo
                ];

                $colIndex = 1;
                foreach ($rowData as $value) {
                    $sheet->setCellValueByColumnAndRow($colIndex++, $rowIndex, $value);
                }

                $rowIndex++;
            }
        }

        // ===== 테두리 =====
        $sheet->getStyle('A1:AH' . ($rowIndex - 1))
            ->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        // ===== 컬럼 폭 자동 =====
        $highestColumn = $sheet->getHighestColumn();
        $highestColumnIndex = Coordinate::columnIndexFromString($highestColumn);
        for ($col = 1; $col <= $highestColumnIndex; $col++) {
            $sheet->getColumnDimensionByColumn($col)->setAutoSize(true);
        }

        // ===== 파일 저장 및 다운로드 =====
        $tmpFilePath = sys_get_temp_dir() . '/orders.xlsx';
        $writer = new Xlsx($spreadsheet);
        $writer->save($tmpFilePath);

        $filename = 'orders_' . date('Y-m-d_H-i-s') . '.xlsx';

        return Response::download(
            $tmpFilePath,
            $filename
        );
    }


    public function show(Request $request, string $orderNo)
    {
        return $this->renderDetail($request, $orderNo, 'admin.pages.orders.show');   
    }


    public function edit(Request $request, string $orderNo)
    {
        
        return $this->renderDetail($request, $orderNo, 'admin.pages.orders.edit');   
    }

    public function renderDetail(Request $request,  $orderNo, string $view)
    {
            
        $order = OrderRepository::make()
            ->with([
                'documents',
                'customer',
                'user',
                'items.product.template.fileattachment',
            ])
            ->query()
            ->where('order_no', $orderNo)
            ->firstOrFail();
        
        $orderLogs = OrderLogRepository::make()
            ->query()
            ->with(['user'])
            ->where('order_id', $order->id)
            ->orderByDesc('created_at')
            ->get();

        if ($order->dealer_id) {
            $orderHistories = OrderHistoryRepository::make()
                ->query()
                ->with(['user', 'order', 'dealer.user'])
                ->where('dealer_id', $order->dealer_id)
                ->orderByDesc('created_at')
                ->get();
        } elseif ($order->customer_id) {
            $orderHistories = OrderHistoryRepository::make()
                ->query()
                ->with(['user', 'order', 'customer'])
                ->where('customer_id', $order->customer_id)
                ->orderByDesc('created_at')
                ->get();
        } else {
            $orderHistories = [];
        }

        foreach ($order->items as $item) {
            $type = $item->product->type;
            if ($item->product->type) {
                $item->product->load([$type]);
            }
        };

        $groupedItems = OrderItem::groupBySet($order->items);
        $order->replaceRelation('items', $groupedItems);

        foreach ($order->documents as $document) {
            $subType = $document->type;
            $document->load([$subType]); 
            $subDocument = $document->{$subType};

            if ($subDocument) {
                $subDocument->setRelation('document', $document);
                $order->setRelation($subType, $subDocument);
            }
        }

        $order->forgetRelation('documents');
        
        return $this->render($view, [
            'order' => $order,
            'orderHistories' => $orderHistories,
            'orderLogs' => $orderLogs,
        ]);
    }

    public function restoreItem(string $orderItemId, Request $request)
    {
        return $this->runInTransaction(function () use ($orderItemId) {
            $orderItem = OrderItemRepository::make()
                ->query()
                ->with([
                    'product.template.category',
                    'order.customer.cart'
                ])
                ->find($orderItemId);

            if (!$orderItem) {
                throw new RuntimeException("주문 아이템을 찾을 수 없습니다.");
            }

            if ($orderItem->order->isFinalized()) {
                throw new RuntimeException("주문이 완료된 상태입니다. 더 이상 수정할 수 없습니다.");
            }

            // customer 없을 경우도 생각! 
            $customer = $orderItem->order->customer;
            $cart = $customer->cart;

            if (!$cart) {
                $cart = CartRepository::make()->query()->firstOrCreate([
                    'customer_id' => $customer->id,
                ]);
            }

            $cartItem = $this->restoreProduct($orderItem, $cart);

            return $this->render(null, [
                'cartitem' => $cartItem->toArray(),
            ], '주문 아이템이 장바구니에 복원되었습니다.');
        });
    }

    public function restoreAll(string $orderId, Request $request)
    {
        return $this->runInTransaction(function () use ($orderId) {
            $order = OrderRepository::make()
                ->with([
                    'items.product.template',
                    'customer.cart'
                ])
                ->find($orderId);

            if (!$order) {
                throw new RuntimeException("주문을 찾을 수 없습니다.");
            }

            $customer = $order->customer;
            $cart = $customer->cart;

            if (!$cart) {
                $cart = CartRepository::make()->query()->firstOrCreate([
                    'customer_id' => $customer->id,
                ]);
            }

            $restoredCartItems = [];
            $groupedItems = OrderItem::groupBySet($order->items);

            foreach ($groupedItems as $orderItem) {
                $cartItem = $this->restoreProduct($orderItem, $cart);
                $restoredCartItems[] = $cartItem->toArray();
            }

            return $this->render(null, [
                'cartitems' => $restoredCartItems,
            ], '모든 주문 아이템이 장바구니로 복원되었습니다.');
        });
    }

    private function restoreProduct(OrderItem $orderItem, $cart)
    {
        $orderedProduct = $orderItem->product;
        $productTemplate = $orderedProduct->template;

        // === 1) 새 Product 생성 ===
        $newProductData = [
            'template_id' => $productTemplate->id,
            'name'        => $productTemplate->name,
            'type'        => $orderedProduct->type,
            'code'        => $productTemplate->code,
            'model'       => $productTemplate->model,
            'price'       => $productTemplate->price,
            'description' => $productTemplate->description,
        ];

        $newProduct = new Product($newProductData);
        $newProduct = ProductRepository::make()->save($newProduct);

        if (!$newProduct) {
            throw new RuntimeException("제품 생성에 실패하였습니다.");
        }

        // === 2) 확장 제품 생성 (TPT 방식) ===
        $type = $orderedProduct->type;
        if ($type) {
            $orderedProduct->load([$type]);
            $subEntity = $orderedProduct->{$type};
            if ($subEntity) {
                $subProductClass = get_class($subEntity);
                $repoClass = $subProductClass::repositoryClass();
                $subProductData = $subEntity->getAttributes();
                $subProductData['id'] = $newProduct->id;

                $subEntityNew = new $subProductClass($subProductData);
                $savedSub = $repoClass::make()->save($subEntityNew);

                if (!$savedSub) {
                    throw new RuntimeException("제품 확장 저장에 실패하였습니다.");
                }
            }
        }

        // === 3) CartItem 생성 ===
        $setGroupId = $orderItem->set_group_id ? $orderItem->set_group_id : null;

        $cartItemData = [
            'cart_id'        => $cart->id,
            'product_id'     => $newProduct->id,
            'quantity'       => $orderItem->quantity,
            'set_group_id'   => $setGroupId,
            'set_group_sort' => $orderItem->set_group_sort,
            'is_main_item'   => $orderItem->is_main_item,
        ];

        $cartItem = new CartItem($cartItemData);
        $cartItem = CartItemRepository::make()->save($cartItem);

        if (!$cartItem) {
            throw new RuntimeException("메인 제품 장바구니 추가에 실패하였습니다.");
        }

        // === 4) 세트 아이템 복원 ===
        if ($orderItem->is_main_item && $setGroupId) {
            // 세트 OrderItem 조회
            $sets = OrderItemRepository::make()
                ->query()
                ->with(['product.template.category'])
                ->where('set_group_id', $setGroupId)
                ->where('is_main_item', false)
                ->get();

            $setCartItems = [];

            foreach ($sets as $index => $setItem) {
                $setOrderedProduct = $setItem->product;
                $setProductTemplate = $setOrderedProduct->template;

                // 세트용 새 Product 생성
                $newSetProductData = [
                    'template_id' => $setProductTemplate->id,
                    'name'        => $setProductTemplate->name,
                    'type'        => $setOrderedProduct->type,
                    'code'        => $setProductTemplate->code,
                    'model'       => $setProductTemplate->model,
                    'price'       => $setProductTemplate->price,
                    'description' => $setProductTemplate->description,
                ];

                $newSetProduct = new Product($newSetProductData);
                $newSetProduct = ProductRepository::make()->save($newSetProduct);
                

                if (!$newSetProduct) {
                    throw new RuntimeException("세트 제품 생성에 실패하였습니다.");
                }

                // 확장 테이블 저장
                $setType = $setOrderedProduct->type;
                if ($setType) {
                    $subSetEntity = $setOrderedProduct->{$setType};
                    if ($subSetEntity) {
                        $subSetProductClass = get_class($subSetEntity);
                        $repoClass = $subSetProductClass::repositoryClass();
                        $subSetProductData = $subSetEntity->getAttributes();
                        $subSetProductData['id'] = $newSetProduct->id;

                        $subSetEntityNew = new $subSetProductClass($subSetProductData);
                        $savedSetSub = $repoClass::make()->save($subSetEntityNew);

                        if (!$savedSetSub) {
                            throw new RuntimeException("세트 제품 확장 저장에 실패하였습니다.");
                        }
                    }
                }

                $setCartItemData = [
                    'cart_id'         => $cart->id,
                    'product_id'      => $newSetProduct->id,
                    'quantity'        => $setItem->quantity,
                    'set_group_id'    => $setGroupId,
                    'set_group_sort'  => $setItem->set_group_sort,
                    'is_main_item'    => false,
                ];

                $setCartItem = new CartItem($setCartItemData);
                $setCartItem = CartItemRepository::make()->save($setCartItem);

                if (!$setCartItem) {
                    throw new RuntimeException("세트 장바구니 아이템 저장에 실패하였습니다.");
                }

                $setCartItem->setRelation('product', $newSetProduct);
                $setCartItems[] = $setCartItem;
            }

            // 세트 정렬
            usort($setCartItems, function ($a, $b) {
                return ($a->set_group_sort ?? 0) <=> ($b->set_group_sort ?? 0);
            });

            $cartItem->setRelation('sets', $setCartItems);
        }

        // === 5) 관계 로딩 ===
        $cartItem->load([
            'product.template.fileattachment',
        ]);

        return $cartItem;
    }


}