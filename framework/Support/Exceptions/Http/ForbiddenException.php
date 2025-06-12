<?php

namespace Framework\Support\Exceptions\Http;

use Framework\Support\Exceptions\BaseException;

class ForbiddenException extends BaseException
{
    public function __construct(string $message = '접근이 거부되었습니다.', array $meta = [])
    {
        parent::__construct($message, 403, $meta);
    }
}
