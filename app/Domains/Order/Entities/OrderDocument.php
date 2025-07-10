<?php

namespace App\Domains\Order\Entities;

use Framework\Database\ORM\Entities\Entity;
use App\Domains\Order\Repositories\OrderDocumentRepository;

class OrderDocument extends Entity
{
    protected array $fillable = [
        'order_id',
        'user_id',
        'document_no',
        'type',
        'status',
        'created_at',
    ];

    protected array $casts = [
        'order_id'     => 'int',
        'user_id'      => 'int',
        'document_no'  => 'string',
        'type'         => 'string',
        'status'       => 'string',
        'created_at'   => 'datetime',
    ];

    public static function repositoryClass(): string
    {
        return OrderDocumentRepository::class;
    }

    /**
     * 문서번호 생성 (예: PI-DA001-20250629-00001) => 
     *
     * @param string $documentPrefix
     * @param string|null $dealerCode
     * @return string
     */
    public function generateDocumentNumber(string $documentPrefix, ?string $dealerCode = null): string
    {
        $dealerCode = $dealerCode ?: 'CST';
        // $dateStr = now()->format('Ymd');
        // $prefix = "{$documentPrefix}-{$dealerCode}-{$dateStr}";
        $year = date('Y'); // ex) 2025
        $prefix = "{$dealerCode}-{$year}";

        $row = static::repositoryClass()::make()
            ->query()
            ->where('document_no', 'like', "{$prefix}-%")
            ->orderByDesc('document_no')
            ->first();

        $latestDocumentNo = $row ? $row->document_no : null;

        if ($latestDocumentNo) {
            $lastSeq = (int) substr($latestDocumentNo, strrpos($latestDocumentNo, '-') + 1);
            $nextSeq = $lastSeq + 1;
        } else {
            $nextSeq = 1;
        }

        $sequenceStr = str_pad((string) $nextSeq, 5, '0', STR_PAD_LEFT);

        $documentNo = "{$prefix}-{$sequenceStr}";
        $this->document_no = $documentNo;

        return $documentNo;
    }

}
