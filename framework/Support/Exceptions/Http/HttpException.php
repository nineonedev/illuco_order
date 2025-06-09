<?php

namespace Framework\Support\Exceptions\Http;

use Framework\Support\Exceptions\RenderableException;

class HttpException extends RenderableException
{
    protected int $statusCode;

    public function __construct(string $message = '', int $statusCode = 500, array $meta = [])
    {
        $this->statusCode = $statusCode;
        parent::__construct($message ?: static::getDefaultMessage($statusCode), $statusCode, $meta);
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    protected static function getDefaultMessage(int $code): string
    {
        switch ($code) {
            case 401:
                return 'Unauthorized';
            case 403:
                return 'Forbidden';
            case 404:
                return 'Not Found';
            case 405:
                return 'Method Not Allowed';
            case 429:
                return 'Too Many Requests';
            default:
                return 'Http Error';
        }
    }

}
