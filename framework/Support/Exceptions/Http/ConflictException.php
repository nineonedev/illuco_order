<?php

namespace Framework\Support\Exceptions\Http;

use Framework\Support\Exceptions\BaseException;

class ConflictException extends BaseException
{
    public function __construct(string $message = '충돌이 발생했습니다.', array $meta = [])
    {
        parent::__construct($message, 409, $meta);
    }
}