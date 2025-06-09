<?php

namespace Framework\Support\Exceptions\Http;

class UnprocessableEntityException extends HttpException
{
    public function __construct(string $message = 'Unprocessable Entity', array $meta = [])
    {
        parent::__construct($message, 422, $meta);
    }
}
