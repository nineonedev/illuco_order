<?php

namespace Framework\Support\Exceptions\Http;

class ForbiddenException extends HttpException
{
    public function __construct(string $message = 'Forbidden', array $meta = [])
    {
        parent::__construct($message, 403, $meta);
    }
}
