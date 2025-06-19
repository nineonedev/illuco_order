<?php

namespace App\Domains\System\Supports;

use App\Domains\System\Entities\FileAttachment;

class FileAttachmentList
{
    /**
     * @var array<string, FileAttachment>
     */
    protected array $attachments = [];

    public function __construct(array $attachments)
    {
        foreach ($attachments as $attachment) {
            if ($attachment instanceof FileAttachment && $attachment->file_key) {
                $this->attachments[$attachment->file_key] = $attachment;
            }
        }
    }

    /**
     * @return static
     */
    public static function make(array $attachments)
    {
        return new static($attachments);
    }

    /**
     * 특정 key에 해당하는 첨부파일 반환
     */
    public function get(string $key): ?FileAttachment
    {
        return $this->attachments[$key] ?? null;
    }

    public function props(string $key, array $data = []): string
    {
        $attachment = $this->get($key);
        return $attachment 
            ? json(array_merge($attachment->toArray(), $data)) 
            : json(array_merge(['file_key' => $key], $data));    
    }

    /**
     * 전체 첨부파일 리스트 반환
     *
     * @return FileAttachment[]
     */
    public function all(): array
    {
        return array_values($this->attachments);
    }

    /**
     * 사용 중인 file_key 목록 반환
     */
    public function keys(): array
    {
        return array_keys($this->attachments);
    }

    /**
     * file_key => props 배열 형태로 반환
     */
    public function toArray(): array
    {
        $result = [];

        foreach ($this->attachments as $key => $attachment) {
            $result[$key] = $attachment->toArrayIfHasKey();
        }

        return $result;
    }
}
