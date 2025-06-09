<?php

namespace Framework\Support\Exceptions\Http;

class InternalServerErrorException extends HttpException
{
    public function __construct(string $message = 'Internal Server Error', array $meta = [])
    {
        parent::__construct($message, 500, $meta);
    }
}
