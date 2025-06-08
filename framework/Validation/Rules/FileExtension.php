<?php

namespace Framework\Validation\Rules;

class FileExtension extends Rule
{
    protected $extensions;

    public function __construct(...$extensions)
    {
        if (count($extensions) === 1 && is_array($extensions[0])) {
            $this->extensions = $extensions[0];
        } else {
            $this->extensions = $extensions;
        }
    }

    /**
     * Validate that the file has a valid extension.
     *
     * @param mixed $value
     * @return bool
     */
    public function passes($value): bool
    {
        $extension = null;

        // 파일 업로드 배열인 경우
        if (is_array($value) && isset($value['name'])) {
            $extension = pathinfo($value['name'], PATHINFO_EXTENSION);
        } elseif (is_string($value)) {
            $extension = pathinfo($value, PATHINFO_EXTENSION);
        }

        if ($extension === null) {
            return false;
        }

        return in_array(strtolower($extension), array_map('strtolower', $this->extensions), true);
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
