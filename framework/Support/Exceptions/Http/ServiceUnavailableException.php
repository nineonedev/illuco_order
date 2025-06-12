<?php

namespace Framework\Support\Exceptions\Http;

use Framework\Support\Exceptions\BaseException;


class ServiceUnavailableException extends BaseException
{
    public function __construct(string $message = '서비스를 사용할 수 없습니다.', array $meta = [])
    {
        parent::__construct($message, 503, $meta);
    }
}