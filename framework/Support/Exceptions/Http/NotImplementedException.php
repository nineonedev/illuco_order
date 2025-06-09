<?php

namespace Framework\Support\Exceptions\Http;

class NotImplementedException extends HttpException
{
    public function __construct(string $message = 'Not Implemented', array $meta = [])
    {
        parent::__construct($message, 501, $meta);
    }
}
