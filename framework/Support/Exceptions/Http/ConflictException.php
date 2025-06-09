<?php

namespace Framework\Support\Exceptions\Http;

class ConflictException extends HttpException
{
    public function __construct(string $message = 'Conflict', array $meta = [])
    {
        parent::__construct($message, 409, $meta);
    }
}
