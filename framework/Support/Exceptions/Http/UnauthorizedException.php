<?php

namespace Framework\Support\Exceptions\Http;

class UnauthorizedException extends HttpException
{
    public function __construct(string $message = 'Unauthorized', array $meta = [])
    {
        parent::__construct($message, 401, $meta);
    }
}
