<?php

namespace Framework\Support\Exceptions\Http;

class NotFoundException extends HttpException
{
    public function __construct(string $message = 'Not Found', array $meta = [])
    {
        parent::__construct($message, 404, $meta);
    }
}
