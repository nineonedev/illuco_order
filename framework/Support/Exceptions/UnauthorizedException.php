<?php

namespace Framework\Support\Exceptions; 

class UnauthorizedException extends BaseException
{
    public function __construct(string $message = "Unauthorized")
    {
        parent::__construct($message, 401);
    }
}