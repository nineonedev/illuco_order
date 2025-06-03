<?php

namespace Framework\Support\Exceptions\Http;

use Framework\Support\Exceptions\HttpException;

class MethodNotAllowedException extends HttpException
{
    public function __construct(string $message = "Method Not Allowed", array $context = [])
    {
        parent::__construct($message, 405, $context);
    }
}
