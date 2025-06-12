<?php

namespace Framework\Support\Exceptions\Http;

use Framework\Support\Exceptions\BaseException;

class NotFoundException extends BaseException
{
    public function __construct(string $message = '요청한 리소스를 찾을 수 없습니다.', array $meta = [])
    {
        parent::__construct($message, 404, $meta);
    }
}