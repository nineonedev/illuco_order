<?php

namespace App\Domains\Order\Entities;

use App\Domains\Order\Enums\OrderStatus;
use App\Domains\Order\Repositories\OrderLogRepository;
use App\Domains\Order\Repositories\OrderRepository;
use App\Domains\Product\Repositories\ProductRepository;
use App\Domains\User\Enums\UserType;
use App\Domains\User\Repositories\UserRepository;
use App\Supports\Mailer;
use Framework\Database\ORM\Entities\Entity;
use RuntimeException;

class Order extends Entity
{
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
        'created_at'  => 'date',

        'total_sales'     => 'decimal',
        'month'           => '?int',
        'date'            => 'date',
        'weekday'         => '?int',
        'time_hour'       => '?int',
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

        // if (config->useEmail) JSON으로 해도될듯? 아니면 그냥 row로 나눠도 되고 key, value 방식으로 
        $this->sendEmailToEmployee();
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

        foreach ($items as $item) {
            $product = $item->product; 
            /** @var \App\Domains\Product\Entities\Product $product */
            if (!empty($product->serial_number)) {
                continue;
            }

            $product->generateSerialNumber();
            $product = ProductRepository::make()->save($product); 
            
            if (!$product) {
                throw new RuntimeException("제품 시리얼 번호 생성에 실패하였습니다. 잠시 후 다시 시도해주세요.");
            }
        }
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
