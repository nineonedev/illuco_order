<?php

namespace Framework\Support\Exceptions\Http;

class MethodNotAllowedException extends HttpException
{
    public function __construct(string $message = 'Method Not Allowed', array $meta = [])
    {
        parent::__construct($message, 405, $meta);
    }
}
