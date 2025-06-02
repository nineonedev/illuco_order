<?php

namespace Framework\Validation\Rules;

class FileExtension extends Rule
{
    protected $extensions;

    public function __construct(array $extensions)
    {
        $this->extensions = $extensions;
    }

    /**
     * Validate that the file has a valid extension.
     *
     * @param mixed $value
     * @return bool
     */
    public function passes($value): bool
    {
        $extension = pathinfo($value, PATHINFO_EXTENSION);
        return in_array(strtolower($extension), $this->extensions);
    }

    /**
     * Get the error message for the validation rule.
     *
     * @return string
     */
    public function message(): string
    {
        return lang('rule.file_extension');
    }
}
