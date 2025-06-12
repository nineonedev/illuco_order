<?php

namespace Framework\Support\Exceptions;

use Exception;

abstract class BaseException extends Exception
{
    protected array $meta;

    public function __construct(string $message = '', int $code = 500, array $meta = [])
    {
        parent::__construct($message, $code);
        $this->meta = $meta;
    }

    public function getMeta(): array
    {
        return $this->meta;
    }

    public function renderHtml(): string
    {
        return render(config('path.error'), [
            'code' => $this->getCode(),
            'message' => $this->getMessage(),
            'meta' => $this->getMeta(),
        ]);
    }

    public function renderJson(): array
    {
        return [
            'success' => false,
            'message' => $this->getMessage(),
            'meta' => $this->getMeta(),
        ];
    }
}