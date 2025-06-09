<?php

namespace Framework\Support\Exceptions\Http;

class NotAcceptableException extends HttpException
{
    public function __construct(string $message = 'Not Acceptable', array $meta = [])
    {
        parent::__construct($message, 406, $meta);
    }
}
