<?php

namespace Framework\Support\Exceptions\Http;

class TooManyRequestsException extends HttpException
{
    public function __construct(string $message = 'Too Many Requests', array $meta = [])
    {
        parent::__construct($message, 429, $meta);
    }
}
