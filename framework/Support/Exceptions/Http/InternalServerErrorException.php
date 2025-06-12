<?php

namespace Framework\Support\Exceptions\Http;

use Framework\Support\Exceptions\BaseException;

class InternalServerErrorException extends BaseException
{
    public function __construct(string $message = '서버 내부 오류입니다.', array $meta = [])
    {
        parent::__construct($message, 500, $meta);
    }
}
