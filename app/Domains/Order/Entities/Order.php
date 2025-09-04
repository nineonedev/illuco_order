<?php

namespace App\Domains\Order\Entities;

use App\Domains\Order\Enums\OrderStatus;
use App\Domains\Order\Repositories\OrderLogRepository;
use App\Domains\Order\Repositories\OrderRepository;
use App\Domains\Product\Entities\ProductSerial;
use App\Domains\Product\Repositories\ProductRepository;
use App\Domains\Product\Repositories\ProductSerialRepository;
use App\Domains\User\Enums\UserType;
use App\Domains\User\Repositories\UserRepository;
use App\Supports\Mailer;
use Framework\Database\ORM\Entities\Entity;
use Framework\Database\ORM\Traits\SoftDeletes;
use RuntimeException;

class Order extends Entity
{
    use SoftDeletes;
    
    protected array $fillable = [
        'user_id',
        'customer_id',
        'dealer_id',
        'order_no',
        'orderer_name',
        'orderer_email',
        'orderer_phone',
        'memo',
        'order_status',
        'total_amount',
        'payment_date',
        'delivery_date',
        'shipping_date',
        'canceled_at',
        'created_at',
        
        'total_sales',
        'month',
        'date',
        'weekday',
        'time_hour',
        'use_remarks',
    ];

    protected array $casts = [
        'user_id'        => 'int',
        'customer_id'    => '?int',
        'dealer_id'      => '?int',
        'orderer_name'   => 'string',
        'orderer_email'  => 'string',
        'orderer_phone'  => 'string',
        'memo'           => 'string',
        'order_status'   => 'string',
        'total_amount'   => 'decimal',
        'payment_date'   => 'date',
        'delivery_date'  => 'date',
        'shipping_date'  => 'date',
        'canceled_at'  => 'datetime',

        'total_sales'     => 'decimal',
        'month'           => '?int',
        'date'            => 'date',
        'weekday'         => '?int',
        'time_hour'       => '?int',
        'use_remarks'   => 'bool',
    ];

    public static function repositoryClass(): string
    {
        return OrderRepository::class;
    }

    /**
     * 주문 상태가 'preparing' 또는 'shipped'일 경우 수정 불가
     */
    public function isFinalized(): bool
    {
        return !in_array($this->order_status, [OrderStatus::NEW, OrderStatus::CONFIRMED]);
    }

    /**
     * 주문 상태가 'canceled'일 경우 취소 상태
     */
    public function isCanceled(): bool
    {
        return $this->order_status === OrderStatus::CANCELED;
    }

    public function setStatus(string $status)
    {
        if (!in_array($status, OrderStatus::all())) {
            return; 
        }

        if ($status === OrderStatus::CANCELED) {
            $this->canceled_at = now();
        }

        if ($status === OrderStatus::PREPARING) {
            $this->generateProductSerialNumbers();
        }

        if (!$this->getRelation('items')) {
            $this->load([
                'items.product.template.fileattachment',
            ]);
        } 

        if ($this->getRelation('customer')) {
            $this->load(['customer']);
        }

        if ($this->getRelation('dealer')) {
            $this->load(['dealer']);
        }

        if ($this->status !== $status) {
            $orderLogData = [
                'order_id' => $this->id, 
                'user_id' => user()->id,
                'status' => $status,
                'previous_status' => $this->order_status,
            ];

            $orderLog = new OrderLog($orderLogData);
            $orderLog = OrderLogRepository::make()->save($orderLog);

            if (!$orderLog) {
                throw new RuntimeException("오더 상태 기록에 실패하였습니다.");
            }
        }

        $this->order_status = $status; 

        $success = OrderRepository::make()->save($this);
        if (!$success) {
            throw new RuntimeException("오더 상태변경에 실패하였습니다.");
        }

        // if (config->useEmail) JSON으로 해도될듯? 아니면 그냥 row로 나눠도 되고 key, value 방식으로 
        if (config('app.mail.order')) {
            $this->sendEmailToEmployee();
        }
    }

    public function sendEmailToEmployee()
    {
        $employees = UserRepository::make()
            ->query()
            ->with(['roles'])
            ->where('type', UserType::EMPLOYEE)
            ->get();

        if (!$employees || count($employees) === 0) {
            return;
        }

        $recipients = [];

        foreach ($employees as $emp) {
            $recipients[] = [
                'email' => $emp->email,
                'name'  => $emp->name,
            ];
        }

        $this->load([
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
        ]);

        $subject = "[일루코] 주문 상태 변경 알림";
        $body = render('admin.mails.order', ['order' => $this]);

        $mailer = new Mailer();
        $mailer->sendBulk($recipients, $subject, $body); 
    }

    public function generateProductSerialNumbers(): void
    {
        if (!$this->getRelation('items')) {
            $this->load(['items.product']);
        }

        $items = $this->items ?? [];
        if (!$items) return;

        // prefix별 로컬 커서 캐시: [prefix => currentMaxSeq]
        $seqCursor = [];

        /** @var \App\Domains\Order\Entities\OrderItem $item */
        foreach ($items as $item) {
            $product = $item->product;
            if (!$product) {
                continue;
            }

            // 이미 이 주문아이템에 대해 생성된 시리얼 수
            $alreadyCount = (int) ProductSerialRepository::make()
                ->query()
                ->where('order_item_id', $item->id)
                ->count();

            $need = max(0, (int)$item->quantity - $alreadyCount);
            if ($need <= 0) {
                continue;
            }

            // prefix 계산 (제품코드 + 특수코드 + 연도 2자리)
            $special = $item->special_code ?? null; // 없으면 NNN
            $special = $special ?: 'NNN';
            $year    = date('y');
            $prefix  = $product->code . $special . $year;
            $rev     = 'A'; // 필요시 규칙에 따라 변경

            // prefix별 현재 최대 시퀀스를 한 번만 조회
            if (!array_key_exists($prefix, $seqCursor)) {
                $latest = ProductSerial::repositoryClass()::make()
                    ->query()
                    ->where('product_id', $product->id)
                    ->where('serial_number', 'LIKE', "{$prefix}%")
                    ->orderByDesc('serial_number')
                    ->first();

                if ($latest) {
                    $seqPart = substr($latest->serial_number, strlen($prefix), 6);
                    $seqCursor[$prefix] = (int) $seqPart; // 현재 최대값
                } else {
                    $seqCursor[$prefix] = 0; // 아직 없음
                }
            }

            // 필요 수만큼 생성
            for ($i = 0; $i < $need; $i++) {
                $attempt     = 0;
                $maxAttempts = 10;

                while (true) {
                    $attempt++;

                    // 로컬 커서를 1 올려서 새 번호 생성
                    $seqCursor[$prefix] = $seqCursor[$prefix] + 1;
                    $seqNum             = str_pad((string)$seqCursor[$prefix], 6, '0', STR_PAD_LEFT);
                    $serialNumber       = $prefix . $seqNum . $rev;

                    try {
                        $entity = ProductSerial::make([
                            'product_id'    => $product->id,
                            'order_item_id' => $item->id,
                            'serial_number' => $serialNumber,
                            'status'        => 'sold', // 필요 시 'created' 등 단계 분리
                        ]);

                        $saved = ProductSerialRepository::make()->save($entity);
                        if (!$saved) {
                            throw new RuntimeException('시리얼번호 저장 실패');
                        }

                        // 성공
                        break;

                    } catch (\Throwable $e) {
                        // 중복이면 커서만 올리고 재시도 (DB 재조회 X)
                        $msg = $e->getMessage();
                        $code = ($e instanceof \PDOException) ? $e->getCode() : null;
                        $isDup = ($code === '23000') && (
                            strpos($msg, '1062') !== false || stripos($msg, 'Duplicate entry') !== false
                        );

                        if ($isDup && $attempt < $maxAttempts) {
                            // 바로 다음 번호로 재시도
                            continue;
                        }

                        throw $e;
                    }
                } // while
            } // for need
        } // foreach items
    }




    /**
     * 주문번호 생성 (예: OR-DA001-20250702-00001)
     *
     * @param string|null $dealerCode
     * @return string
     */
    public function generateOrderNumber(?string $dealerCode = null): string
    {
        $dealerCode = $dealerCode ?: 'CST';
        // $dateStr = now()->format('Ymd');
        $dateStr = now()->format('Y');
        // $prefix = "OR-{$dealerCode}-{$dateStr}";
        // $prefix = "OR-{$dealerCode}-{$dateStr}";
        $prefix = "{$dealerCode}-{$dateStr}";

        $row = static::repositoryClass()::make()
            ->query()
            ->where('order_no', 'like', "{$prefix}-%")
            ->orderByDesc('order_no')
            ->first();

        $latestOrderNo = $row ? $row->order_no : null;

        if ($latestOrderNo) {
            $lastSeq = (int) substr($latestOrderNo, strrpos($latestOrderNo, '-') + 1);
            $nextSeq = $lastSeq + 1;
        } else {
            $nextSeq = 1;
        }

        $sequenceStr = str_pad((string) $nextSeq, 5, '0', STR_PAD_LEFT);

        $orderNo = "{$prefix}-{$sequenceStr}";
        $this->order_no = $orderNo; 

        return $orderNo; 
    }

}
