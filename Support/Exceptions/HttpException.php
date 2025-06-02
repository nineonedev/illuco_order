<?php

namespace Framework\Support\Exceptions; 

class HttpException extends BaseException 
{
    protected int $statusCode;

    public function __construct(
        string $message = "", 
        int $statusCode = 500,
        array $context = []
    )
    {
        parent::__construct($message, $statusCode, $context); 
        $this->statusCode = $statusCode; 
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }
}