<?php

namespace Framework\Validation\Rules;

class FileSize extends Rule
{
    protected $maxSize;

    public function __construct($maxSize)
    {
        $this->maxSize = $maxSize;
    }

    /**
     * Validate that the file size is less than or equal to the max size.
     *
     * @param mixed $value
     * @return bool
     */
    public function passes($value): bool
    {
        return filesize($value) <= $this->maxSize;
    }

    /**
     * Get the error message for the validation rule.
     *
     * @return string
     */
    public function message(): string
    {
        return lang('rule.file_size', [$this->maxSize]);
    }
}
