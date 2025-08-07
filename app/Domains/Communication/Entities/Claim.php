<?php 

namespace App\Domains\Communication\Entities;

use App\Domains\Communication\Repositories\ClaimRepository;
use Framework\Database\ORM\Entities\Entity;
use Framework\Database\ORM\Traits\SoftDeletes;

class Claim extends Entity
{
    use SoftDeletes;
    protected array $fillable = [
        // 스냅샷: 제품 정보
        'product_name',
        'product_code',
        'product_model',
        'product_serial_number',
        'product_description',

        // 관계
        'user_id',
        'dealer_id',

        // 고객 입력 정보 (스냅샷)
        'customer_name',
        'customer_email',
        'customer_phone',

        'order_no',

        // 문의 정보
        'title',
        'content',

        // 상태
        'status',
        'created_at'
    ];

    protected array $casts = [
        'user_id'    => 'int',
        'dealer_id'  => '?int',
    ];

    public static function repositoryClass(): string
    {
        return ClaimRepository::class;
    }
}
