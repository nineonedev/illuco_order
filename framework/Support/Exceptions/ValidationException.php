<?php 

namespace Framework\Support\Exceptions;

class ValidationException extends BaseException
{
    protected array $errors;

    public function __construct(string $message = '유효성 검사 실패', array $errors = [], array $meta = [])
    {
        parent::__construct($message, 422, $meta);
        $this->errors = $errors;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function renderJson(): array
    {
        return [
            'success' => false,
            'message' => $this->getMessage(),
            'errors'  => $this->getErrors(),
            'meta'    => $this->getMeta(),
        ];
    }

    public function renderHtml(): string
    {
        return render('supports/error', [
            'code' => $this->getCode(),
            'message' => $this->getMessage(),
            'meta' => $this->getMeta(),
            'errors' => $this->getErrors(),
        ]);
    }
}