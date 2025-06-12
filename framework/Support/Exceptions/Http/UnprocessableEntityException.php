<?php

namespace Framework\Support\Exceptions\Http;

use Framework\Support\Exceptions\BaseException;
class UnprocessableEntityException extends BaseException
{
    public function __construct(string $message = '요청을 처리할 수 없습니다.', array $meta = [])
    {
        parent::__construct($message, 422, $meta);
    }
}
