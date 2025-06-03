<?php 

namespace Framework\Support\Exceptions\Http;

use Framework\Support\Exceptions\HttpException;

class UnauthorizedException extends HttpException
{
    public function __construct(string $message = "Unauthorized", array $context = [])
    {
        parent::__construct($message, 401, $context);
    }
}
