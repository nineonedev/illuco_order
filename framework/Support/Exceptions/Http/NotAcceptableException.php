<?php

namespace Framework\Support\Exceptions\Http;

use Framework\Support\Exceptions\BaseException;

class NotAcceptableException extends BaseException
{
    public function __construct(string $message = '요청한 응답을 제공할 수 없습니다.', array $meta = [])
    {
        parent::__construct($message, 406, $meta);
    }
}
