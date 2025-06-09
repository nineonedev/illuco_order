<?php

namespace Framework\Support\Exceptions\Http;

class GoneException extends HttpException
{
    public function __construct(string $message = 'Gone', array $meta = [])
    {
        parent::__construct($message, 410, $meta);
    }
}
