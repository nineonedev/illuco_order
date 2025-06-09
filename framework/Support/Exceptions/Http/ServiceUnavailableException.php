<?php

namespace Framework\Support\Exceptions\Http;

class ServiceUnavailableException extends HttpException
{
    public function __construct(string $message = 'Service Unavailable', array $meta = [])
    {
        parent::__construct($message, 503, $meta);
    }
}
