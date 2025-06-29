<?php 

namespace App\Domains\Communication\Entities;

use App\Domains\Communication\Enums\NoticeStatus;
use App\Domains\Communication\Repositories\NoticeRepository;
use Framework\Database\ORM\Entities\Entity;

class Notice extends Entity
{

    protected array $fillable = [
        'user_id',
        'title', 
        'content',
        'visible_from',
        'visible_to',
        'is_pinned',
        'status',
    ]; 

    protected array $casts = [
        'user_id' => 'int',
        'is_pinned' => 'bool',
        'visible_from' => 'datetime',
        'visible_to' => 'datetime',
    ];

    public static function repositoryClass(): string
    {
        return NoticeRepository::class;
    }
    
    // --- 상태 판별 메서드 ---
    public function isDraft(): bool
    {
        return $this->status === NoticeStatus::DRAFT;
    }

    public function isPublished(): bool
    {
        return $this->status === NoticeStatus::PUBLISHED;
    }

    public function isArchived(): bool
    {
        return $this->status === NoticeStatus::ARCHIVED;
    }

    public function isScheduled(): bool
    {
        return $this->status === NoticeStatus::SCHEDULED;
    }

    // --- 현재 시간 기준 노출 가능 여부 판단 ---
    public function isVisibleNow(): bool
    {
        if (!$this->isPublished()) return false;

        $now = now();

        if ($this->visible_from && $now < new \DateTime($this->visible_from)) {
            return false;
        }

        if ($this->visible_to && $now > new \DateTime($this->visible_to)) {
            return false;
        }

        return true;
    }
}