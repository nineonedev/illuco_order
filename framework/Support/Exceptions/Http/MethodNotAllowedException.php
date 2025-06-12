<?php

namespace Framework\Support\Exceptions\Http;

use Framework\Support\Exceptions\BaseException;
class MethodNotAllowedException extends BaseException
{
    public function __construct(string $message = '허용되지 않은 HTTP 메서드입니다.', array $meta = [])
    {
        parent::__construct($message, 405, $meta);
    }
}