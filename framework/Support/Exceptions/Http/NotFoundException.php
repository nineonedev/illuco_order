<?php

namespace Framework\Support\Exceptions\Http;

use Framework\Support\Exceptions\HttpException;

class NotFoundException extends HttpException
{
    public function __construct(string $message = "Not Found", array $context = [])
    {
        parent::__construct($message, 404, $context);
    }
}
