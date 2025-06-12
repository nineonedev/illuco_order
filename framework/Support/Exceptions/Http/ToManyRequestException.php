<?php

namespace Framework\Support\Exceptions\Http;

use Framework\Support\Exceptions\BaseException;
class TooManyRequestException extends BaseException
{
    public function __construct(string $message = '요청이 너무 많습니다.', array $meta = [])
    {
        parent::__construct($message, 429, $meta);
    }
}