<?php

namespace App\Domains\Order\Enums;

class OrderStatus
{
    /**
     * 주문 접수됨
     * 대리점에서 구매 확정 시
     */
    const NEW = 'new';

    /**
     * 주문 확인됨
     * 일루코에서 내용 확인부터 생산의뢰서 작성 전까지
     */
    const CONFIRMED = 'confirmed';
        
    /**
     * 상품 준비 중 => 대리점에서 수정불가
     * 생산의뢰서 작성 시 (상품 준비 중)
     */
    const PREPARING = 'preparing';

    /**
     * 출고 완료
     * 운송장 업로드 시 
     */
    const SHIPPED = 'shipped';

    /**
     * 주문 취소
     * 오더 비움
     */
    const CANCELED = 'canceled';


    // 오더 완료
    const COMPLETED = 'completed'; 


    // 일루코에서 취소
    const REJECTED = 'rejected'; 

}