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
        $this->errors = $errors;

        $firstMessage = $message ?? lang('validation.validation_failed');

        foreach ($errors as $fieldErrors) {
            if (is_array($fieldErrors)) {
                $firstMessage = reset($fieldErrors); // 첫 번째 에러 메시지 사용
                break;
            }
        }

        parent::__construct($firstMessage , 422, $meta);
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    // JSON 응답 커스텀: errors 필드 추가
    protected function json(): array
    {
        return [
            'message' => $this->getMessage(),
            'errors' => $this->getErrors(),
        ];
    }
}