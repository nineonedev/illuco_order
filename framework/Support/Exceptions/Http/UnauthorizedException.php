<?php

namespace Framework\Support\Exceptions\Http;

use Framework\Support\Exceptions\BaseException;

class UnauthorizedException extends BaseException
{
    public function __construct(string $message = '인증이 필요합니다.', array $meta = [])
    {
        parent::__construct($message, 401, $meta);
    }
}
