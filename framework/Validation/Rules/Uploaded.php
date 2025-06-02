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
        // Check if the value is an uploaded file
        return is_uploaded_file($value);
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
