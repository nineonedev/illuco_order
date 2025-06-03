<?php 

namespace Framework\Support\Exceptions\Http;

use Framework\Support\Exceptions\HttpException;

class ForbiddenException extends HttpException
{
    public function __construct(string $message = "Forbidden", array $context = [])
    {
        parent::__construct($message, 403, $context);
    }
}
