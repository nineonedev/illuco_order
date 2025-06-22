<?php

namespace App\Domains\Order\Entities;

use App\Domains\Order\Repositories\OrderRepository;
use Framework\Database\ORM\Entities\Entity;

class Order extends Entity
{
    /**
     * 주문 접수됨
     * 대리점에서 구매 확정 시
     */
    const STATUS_RECEIVED = 'received';

    /**
     * 주문 확인됨
     * 일루코에서 내용 확인부터 생산의뢰서 작성 전까지
     */
    const STATUS_CONFIRMED = 'confirmed';
     
    /**
     * 상품 준비 중 => 대리점에서 수정불가
     * 생산의뢰서 작성 시 (상품 준비 중)
     */
    const STATUS_PREPARING = 'preparing';

    /**
     * 출고 완료
     * 운송장 업로드 시 
     */
    const STATUS_SHIPPED = 'shipped';

    /**
     * 주문 취소
     * 오더 비움
     */
    const STATUS_CANCELED = 'canceled';


    protected array $fillable = [
        'user_id',
        'customer_id',
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
        return in_array($this->order_status, [Order::STATUS_PREPARING, Order::STATUS_SHIPPED]);
    }

    /**
     * 주문 상태가 'canceled'일 경우 취소 상태
     */
    public function isCanceled(): bool
    {
        return $this->order_status === Order::STATUS_CANCELED;
    }

    /**
     * 주문 상태가 변경 가능한지 확인 (주문 상태가 'received' 또는 'confirmed'일 때만 상태 변경 가능)
     */
    public function isStatusChangeable(): bool
    {
        return in_array($this->order_status, [Order::STATUS_RECEIVED, Order::STATUS_CONFIRMED]);
    }
}