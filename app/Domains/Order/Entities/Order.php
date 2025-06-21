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
}