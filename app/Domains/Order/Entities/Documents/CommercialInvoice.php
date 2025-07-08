<?php

namespace App\Domains\Order\Entities\Documents;

use App\Domains\Order\Repositories\Documents\CommercialInvoiceRepository;
use Framework\Database\ORM\Entities\Entity;

class CommercialInvoice extends Entity
{
    protected array $fillable = [
        'id',

        // BILL TO
        'bill_to_name',
        'bill_to_address',
        'bill_to_tel',
        'bill_to_attn',
        'bill_to_email',

        // SHIP TO
        'ship_to_name',
        'ship_to_address',
        'ship_to_tel',
        'ship_to_attn',
        'ship_to_email',

        // 문서 정보
        'ref_no',
        'invoice_date',
        'pi_no',
        'po_no',

        // 조건 정보
        'carrier',
        'estimated_delivery_date',
        'payment_terms',
        'price_terms',
        'country_of_origin',
        'currency',

        // 하단 정보 (HS Code 등)
        'hs_code',
        'dev',
        'lst',
        'ein',
    ];

    protected array $casts = [
        'id'                         => 'int',

        'bill_to_name'               => 'string',
        'bill_to_address'            => 'string',
        'bill_to_tel'                => 'string',
        'bill_to_attn'               => 'string',
        'bill_to_email'              => 'string',

        'ship_to_name'               => 'string',
        'ship_to_address'            => 'string',
        'ship_to_tel'                => 'string',
        'ship_to_attn'               => 'string',
        'ship_to_email'              => 'string',

        'ref_no'                     => 'string',
        'invoice_date'               => 'date',
        'pi_no'                      => 'string',
        'po_no'                      => 'string',

        'carrier'                    => 'string',
        'estimated_delivery_date'    => 'string',
        'payment_terms'              => 'string',
        'price_terms'                => 'string',
        'country_of_origin'          => 'string',
        'currency'                   => 'string',

        'hs_code'                    => 'string',
        'dev'                        => 'string',
        'lst'                        => 'string',
        'ein'                        => 'string',
    ];

    public static function repositoryClass(): string
    {
        return CommercialInvoiceRepository::class;
    }

    public static function alias(): string
    {
        return 'commercial_invoice';
    }

    public static function code(): string
    {
        return 'CI';
    }

    /**
     * 총 수량 계산
     *
     * @return int
     */
    public function getTotalQuantity(): int
    {
        $items = $this->order->items ?? [];
        $sum = 0;

        foreach ($items as $item) {
            $sum += $item->quantity ?? 0;
        }

        return $sum;
    }

    /**
     * Sub Total 계산
     *
     * @return float
     */
    public function getSubTotal(): float
    {
        $items = $this->order->items ?? [];
        $sum = 0;

        foreach ($items as $item) {
            $sum += $item->total_price ?? 0;
        }

        return round($sum, 2);
    }

    /**
     * Grand Total 계산
     *
     * @return float
     */
    public function getGrandTotal(): float
    {
        $freightCharge = $this->freight_charge ?? 0;
        return round($this->getSubTotal() + $freightCharge, 2);
    }
}
