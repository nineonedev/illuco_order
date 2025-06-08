<?php

namespace Framework\Validation\Rules;

class UploadedOk extends Rule
{
    /**
     * Validate that the file upload was successful.
     *
     * @param mixed $value
     * @return bool
     */
    public function passes($value): bool
    {
        if (is_array($value) && isset($value['error'])) {
            return $value['error'] === UPLOAD_ERR_OK;
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
        return lang('rule.uploaded_ok');
    }
}
