<?php 

namespace Framework\Support\Exceptions\Http;

use Framework\Support\Exceptions\HttpException;

class InternalServerErrorException extends HttpException
{
    public function __construct(string $message = "Internal Server Error", array $context = [])
    {
        parent::__construct($message, 500, $context);
    }
}
