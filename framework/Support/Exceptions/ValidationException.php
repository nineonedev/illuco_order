<?php

namespace Framework\Support\Exceptions;

class ValidationException extends BaseException
{
    protected array $errors;

    public function __construct(
        ?string $message = null,
        array $errors = [],
        array $meta = []
    ) {
        parent::__construct($message ?? lang('validation.validation_failed'), 422, $meta);
        $this->errors = $errors;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    // JSON 응답 커스텀: errors 필드 추가
    protected function json(): array
    {
        return [
            'errors' => $this->getErrors(),
        ];
    }
}