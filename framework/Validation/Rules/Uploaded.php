<?php

namespace Framework\Validation\Rules;

class Uploaded extends Rule
{
    /**
     * Validate that a file has been uploaded.
     *
     * @param mixed $value
     * @return bool
     */
    public function passes($value): bool
    {
        // 파일 업로드 배열을 받으면 tmp_name을 사용
        if (is_array($value) && isset($value['tmp_name'])) {
            return is_uploaded_file($value['tmp_name']);
        }
        // 혹시 문자열 경로가 직접 넘어올 때(예외)
        if (is_string($value)) {
            return is_uploaded_file($value);
        }

        return false; 
    }

    /**
     * Get the error message for the validation rule.
     *
     * @return string
     */
    public function message(): string
    {
        return lang('rule.uploaded');
    }
}
