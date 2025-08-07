<?php 

namespace App\Domains\Communication\Entities;

use App\Domains\Communication\Enums\NoticeStatus;
use App\Domains\Communication\Repositories\NoticeRepository;
use App\Domains\User\Enums\UserType;
use App\Domains\User\Repositories\UserRepository;
use App\Supports\Mailer;
use Framework\Database\ORM\Entities\Entity;
use RuntimeException;

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
        'created_at',
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

    public function mailToDealers(): void
    {
        // Dealer 사용자 목록 가져오기
        $dealers = UserRepository::make()
            ->query()
            ->with(['roles'])
            ->where('type', UserType::DEALER)
            ->get();

        if (!$dealers || count($dealers) === 0) {
            return;
        }

        $recipients = [];

        foreach ($dealers as $dealer) {
            $recipients[] = [
                'email' => $dealer->email,
                'name'  => $dealer->name,
            ];
        }

        $subject = "[일루코] 새로운 공지사항이 등록되었습니다.";
        $body = render('admin.mails.notice', ['notice' => $this]);

        $mailer = new Mailer();
        $mailer->sendBulk($recipients, $subject, $body);
    }
}