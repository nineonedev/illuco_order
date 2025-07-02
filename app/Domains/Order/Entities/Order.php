<?php

namespace App\Domains\Order\Entities;

use App\Domains\Order\Enums\OrderStatus;
use App\Domains\Order\Repositories\OrderRepository;
use Framework\Database\ORM\Entities\Entity;

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
        'created_at',
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
        return in_array($this->order_status, [OrderStatus::PREPARING, OrderStatus::SHIPPED]);
    }

    /**
     * 주문 상태가 'canceled'일 경우 취소 상태
     */
    public function isCanceled(): bool
    {
        return $this->order_status === OrderStatus::CANCELED;
    }

    /**
     * 주문 상태가 변경 가능한지 확인 (주문 상태가 'received' 또는 'confirmed'일 때만 상태 변경 가능)
     */
    public function isStatusChangeable(): bool
    {
        return in_array($this->order_status, [OrderStatus::NEW, OrderStatus::CONFIRMED]);
    }

    /**
     * 주문번호 생성 (예: OR-DA001-20250702-00001)
     *
     * @param string|null $dealerCode
     * @return string
     */
    public static function generateOrderNumber(?string $dealerCode = null): string
    {
        $dealerCode = $dealerCode ?: 'CST';
        $dateStr = now()->format('Ymd');
        $prefix = "OR-{$dealerCode}-{$dateStr}";

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

        return "{$prefix}-{$sequenceStr}";
    }

}
