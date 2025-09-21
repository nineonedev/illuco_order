<?php

declare(strict_types=1);

namespace App\Domains\User\Entities;

use App\Domains\User\Repositories\DealerMemoRepository;
use Framework\Database\ORM\Entities\Entity;

/**
 * DealerMemo 모델
 *
 * - 테이블: dealer_memos
 * - PK: dealer_id (Dealers와 1:1)
 * - 자동증가 아님 (dealers.id를 공유)
 * - 타임스탬프: created_at, updated_at 사용
 *
 * 컬럼 개요:
 *   dealer_id (PK, FK -> dealers.id)
 *   memo_general (TEXT)          : 일반 메모
 *   memo_production (TEXT)       : 생산팀 메모
 *   is_pinned_general (TINYINT)  : 일반 메모 상단 고정
 *   is_pinned_prod (TINYINT)     : 생산팀 메모 상단 고정
 *   updated_by (BIGINT)          : 마지막 수정 사용자 id
 *   created_at / updated_at (DATETIME)
 */
class DealerMemo extends Entity
{
    /** @var string */
    protected string $table = 'dealer_memos';

    /** @var string Primary Key 컬럼명 */
    protected string $primaryKey = 'dealer_id';

    /** @var bool auto-increment 아님 */
    protected bool $incrementing = false;

    /** @var string PK 타입 */
    protected string $keyType = 'int';

    /** @var bool created_at / updated_at 관리 여부 */
    protected bool $timestamps = true;

    /**
     * 대량 할당 허용 컬럼
     *
     * @var array<int, string>
     */
    protected array $fillable = [
        'dealer_id',
        'memo_general',
        'memo_production',
        'is_pinned_general',
        'is_pinned_prod',
        'updated_by',
        'created_at',
        'updated_at',
    ];

    /**
     * 타입 캐스팅
     *
     * - '?int' 는 nullable int 의미 (예: updated_by)
     *
     * @var array<string, string>
     */
    protected array $casts = [
        'dealer_id'         => 'int',
        'memo_general'      => '?string',
        'memo_production'   => '?string',
        'is_pinned_general' => 'bool',
        'is_pinned_prod'    => 'bool',
        'updated_by'        => '?int',
        'created_at'        => 'datetime',
        'updated_at'        => 'datetime',
    ];

    /**
     * 이 모델이 사용할 기본 Repository 클래스
     */
    public static function repositoryClass(): string
    {
        return DealerMemoRepository::class;
    }

    /* ============================
     * 도메인 헬퍼 (선택)
     * ============================ */

    /**
     * 일반 메모 핀 고정
     */
    public function pinGeneral(): self
    {
        $this->is_pinned_general = true;
        return $this;
    }

    /**
     * 일반 메모 핀 해제
     */
    public function unpinGeneral(): self
    {
        $this->is_pinned_general = false;
        return $this;
    }

    /**
     * 생산팀 메모 핀 고정
     */
    public function pinProduction(): self
    {
        $this->is_pinned_prod = true;
        return $this;
    }

    /**
     * 생산팀 메모 핀 해제
     */
    public function unpinProduction(): self
    {
        $this->is_pinned_prod = false;
        return $this;
    }

    /* ============================
     * 관계(관례상 메소드명 예시)
     * 실제 ORM의 relation 메소드가 다르면
     * 프로젝트 규약에 맞춰 수정하세요.
     * ============================ */

    /**
     * Dealer(대리점) 1:1
     * ex) return $this->belongsTo(Dealer::class, 'dealer_id', 'id');
     */
    public function dealer()
    {
        if (method_exists($this, 'belongsTo')) {
            return $this->belongsTo(Dealer::class, 'dealer_id', 'id');
        }
        // 관계 메소드가 없다면, 프로젝트 ORM 규약에 맞게 구현/삭제
    }

    /**
     * 마지막 수정자(User)
     * ex) return $this->belongsTo(User::class, 'updated_by', 'id');
     */
    public function updatedBy()
    {
        if (method_exists($this, 'belongsTo')) {
            return $this->belongsTo(User::class, 'updated_by', 'id');
        }
    }

    /* ============================
     * 정적 쿼리 스코프(선택)
     * ============================ */

    /**
     * 핀 된 메모 우선 정렬
     *   - 일반 메모 → 생산팀 메모 → 최신 수정순
     * 사용 예: DealerMemo::query()->orderByPinned()->get();
     */
    public static function orderByPinned($query = null)
    {
        $q = $query ?: static::repositoryClass()::queryStatic();
        // ORM이 orderByRaw, orderBy 지원하는지에 따라 조정
        if (method_exists($q, 'orderByRaw')) {
            return $q->orderByRaw('is_pinned_general DESC, is_pinned_prod DESC, updated_at DESC');
        }
        if (method_exists($q, 'orderBy')) {
            return $q
                ->orderBy('is_pinned_general', 'DESC')
                ->orderBy('is_pinned_prod', 'DESC')
                ->orderBy('updated_at', 'DESC');
        }
        return $q;
    }
}
