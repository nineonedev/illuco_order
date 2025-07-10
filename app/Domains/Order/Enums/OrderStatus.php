<?php

namespace App\Domains\Order\Enums;

class OrderStatus
{
    /**
     * new
     * 대리점 - 접수됨
     * 대리점이 주문을 새로 넣은 상태. 아직 일루코가 검토 안 함.
     */
    const NEW = 'new';

    /**
     * confirmed
     * 일루코 - 필수 정보 재확인 후 입금완료
     * 대리점 주문 내용을 검토하고, 견적 확정 및 입금까지 완료. 생산의뢰 전 단계.
     */
    const CONFIRMED = 'confirmed';
        
    /**
     * preparing
     * 일루코 - 상품 준비 중 (생산중)
     * 생산의뢰서 작성 이후 생산 진행 중. 대리점은 수정 불가.
     */
    const PREPARING = 'preparing';

    /**
     * shipped
     * 일루코 - 출고 완료 (배송중)
     * 운송장 업로드 완료. DHL 등 출하 프로세스 시작.
     */
    const SHIPPED = 'shipped';

    /**
     * completed
     * 일루코 - 오더 완료
     * 바이어가 수령 완료하고, 서류 마감·정산 끝남. 클레임 없이 종결된 상태.
     */
    const COMPLETED = 'completed';

    /**
     * canceled
     * 대리점 - 주문 취소
     * 대리점이 잘못 주문했거나 수정을 위해 취소 요청한 경우.
     */
    const CANCELED = 'canceled';

    /**
     * rejected
     * 일루코 - 일루코에서 취소
     * 영업단에서 문제 있다고 판단하거나, 생산 중/후라도 고객 문제 등으로 출하 안 하기로 결정. 재생산이나 대체 오더로 이어질 수 있음.
     */
    const REJECTED = 'rejected';

    /**
     * 모든 상태 반환
     *
     * @return string[]
     */
    public static function all(): array
    {
        return [
            self::NEW,
            self::CONFIRMED,
            self::PREPARING,
            self::SHIPPED,
            self::COMPLETED,
            self::CANCELED,
            self::REJECTED,
        ];
    }
}
