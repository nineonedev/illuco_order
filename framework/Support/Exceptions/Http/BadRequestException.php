<?php 

namespace Framework\Support\Exceptions\Http;

use Framework\Support\Exceptions\HttpException;

class BadRequestException extends HttpException
{
    public function __construct(string $message = "Bad Request", array $context = [])
    {
        parent::__construct($message, 400, $context);
    }
}