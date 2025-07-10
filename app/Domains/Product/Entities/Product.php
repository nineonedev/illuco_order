<?php

namespace App\Domains\Product\Entities;

use App\Domains\Product\Repositories\ProductRepository;
use Framework\Database\ORM\Entities\Entity;

class Product extends Entity
{
    protected array $fillable = [
        'template_id',
        'serial_number',
        'name',
        'type',
        'code',
        'model',
        'price',
        'description',
        'engraving_text',
    ];

    protected array $casts = [
        'template_id' => 'int',
        'price' => 'decimal',
    ];

    public static function repositoryClass(): string
    {
        return ProductRepository::class;
    }

    public function generateSerialNumber(?string $specialCode = null, string $revision = 'A'): string
    {
        $specialCode = $specialCode ?: 'NNN';

        $year = date('y'); // 예: 25

        // prefix = {제품 code}{특수코드}{년도}
        $prefix = $this->code . $specialCode . $year;

        // 시퀀스 찾기 (같은 prefix 중 max 시퀀스 찾음)
        $repo = self::resolveRepository();

        $latest = $repo->query()
            ->where('serial_number', 'LIKE', "{$prefix}%")
            ->orderByDesc('serial_number')
            ->first();

        $nextSeq = 1;

        if ($latest) {
            $latestSerial = $latest->serial_number;

            // 연번 추출
            $seqPart = substr($latestSerial, strlen($prefix), 6);
            $nextSeq = intval($seqPart) + 1;
        }

        $seqNum = str_pad((string)($nextSeq), 6, '0', STR_PAD_LEFT);

        $serial = $prefix . $seqNum . $revision;

        $this->serial_number = $serial;

        return $serial;
    }

}
