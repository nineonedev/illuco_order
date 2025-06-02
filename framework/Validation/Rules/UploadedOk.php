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
        return $value['error'] === UPLOAD_ERR_OK;
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
