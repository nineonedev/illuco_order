<?php 

namespace App\Domains\Communication\Entities;

use App\Domains\Communication\Repositories\ClaimRepository;
use Framework\Database\ORM\Entities\Entity;

class Claim extends Entity
{
    protected array $fillable = [
        // 스냅샷: 제품 정보
        'product_name',
        'product_code',
        'product_model',

        // 관계
        'template_id',
        'user_id',

        // 고객 입력 정보 (스냅샷)
        'customer_name',
        'customer_email',
        'customer_phone',

        // 제품 정보
        'serial_number',
        'description',

        // 상태
        'status',
    ]; 

    protected array $casts = [
        'template_id'   => 'int',
        'user_id'       => 'int',
    ];

    public static function repositoryClass(): string
    {
        return ClaimRepository::class;
    }
}
