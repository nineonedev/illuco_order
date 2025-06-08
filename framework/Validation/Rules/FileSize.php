<?php

namespace Framework\Validation\Rules;

class FileSize extends Rule
{
    protected $maxBytes;

    /**
     * @param int|float $maxSizeMB 메가바이트 단위
     */
    public function __construct($maxSizeMB)
    {
        $this->maxBytes = $maxSizeMB * 1024 * 1024;
    }

    /**
     * @param mixed $value
     * @return bool
     */
    public function passes($value): bool
    {
        if (is_array($value) && isset($value['tmp_name'])) {
            return is_file($value['tmp_name']) && filesize($value['tmp_name']) <= $this->maxBytes;
        }
        if (is_string($value)) {
            return is_file($value) && filesize($value) <= $this->maxBytes;
        }
        return false;
    }


    public function message(): string
    {
        // 예: "10MB"로 보여주기
        return lang('rule.file_size', [$this->maxBytes / (1024 * 1024) . 'MB']);
    }
}
