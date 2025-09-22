<?php


namespace App\Domains\User\Entities;

use App\Domains\Product\Entities\ProductTemplate;
use App\Domains\User\Repositories\DealerPriceRepository;
use Framework\Database\ORM\Entities\Entity;

/**
 * DealerPrice 모델
 *
 * - 테이블: dealer_prices
 * - PK: id (auto-increment)
 * - 타임스탬프: created_at, updated_at
 *
 * 컬럼:
 *   id (PK)
 *   dealer_id (FK -> dealers.id)
 *   product_template_id (FK -> product_templates.id)
 *   price (DECIMAL)
 *   is_active (TINYINT)
 *   created_at / updated_at (DATETIME)
 */
class DealerPrice extends Entity
{
    /** @var string */
    protected string $table = 'dealer_prices';

    /** @var string */
    protected string $primaryKey = 'id';

    /** @var bool */
    protected bool $incrementing = true;

    /** @var string */
    protected string $keyType = 'int';

    /** @var bool */
    protected bool $timestamps = true;

    /** @var array<int, string> */
    protected array $fillable = [
        'dealer_id',
        'product_template_id',
        'price',
        'is_active',
        'created_at',
        'updated_at',
    ];

    /** @var array<string, string> */
    protected array $casts = [
        'id'                  => 'int',
        'dealer_id'           => 'int',
        'product_template_id' => 'int',
        'price'               => 'decimal',
        'is_active'           => 'bool',
        'created_at'          => 'datetime',
        'updated_at'          => 'datetime',
    ];

    public static function repositoryClass(): string
    {
        return DealerPriceRepository::class;
    }

    /* ========= 헬퍼 ========= */

    public function activate(): self
    {
        $this->is_active = true;
        return $this;
    }

    public function deactivate(): self
    {
        $this->is_active = false;
        return $this;
    }

    /** 활성 가격만 우선 정렬(활성 desc, 최신 수정 desc) */
    public static function orderByActive($query = null)
    {
        $q = $query ?: static::repositoryClass()::queryStatic();
        if (method_exists($q, 'orderBy')) {
            return $q->orderBy('is_active', 'DESC')
                        ->orderBy('updated_at', 'DESC');
        }
        return $q;
    }

    /* ========= 관계 ========= */

    // ex) return $this->belongsTo(Dealer::class, 'dealer_id', 'id');
    public function dealer()
    {
        if (method_exists($this, 'belongsTo')) {
            return $this->belongsTo(Dealer::class, 'dealer_id', 'id');
        }
    }

    // ex) return $this->belongsTo(ProductTemplate::class, 'product_template_id', 'id');
    public function productTemplate()
    {
        if (method_exists($this, 'belongsTo')) {
            return $this->belongsTo(ProductTemplate::class, 'product_template_id', 'id');
        }
    }
}
