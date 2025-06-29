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
        'customer_id'    => 'int',
        'dealer_id'    => 'int',
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
        return in_array($this->order_status, [OrderStatus::RECEIVED, OrderStatus::CONFIRMED]);
    }
}