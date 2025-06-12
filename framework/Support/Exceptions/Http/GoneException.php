<?php

namespace Framework\Support\Exceptions\Http;

use Framework\Support\Exceptions\BaseException;

class GoneException extends BaseException
{
    public function __construct(string $message = '리소스를 더 이상 사용할 수 없습니다.', array $meta = [])
    {
        parent::__construct($message, 410, $meta);
    }
}