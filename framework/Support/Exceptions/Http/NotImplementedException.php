<?php

namespace Framework\Support\Exceptions\Http;

use Framework\Support\Exceptions\BaseException;


class NotImplementedException extends BaseException
{
    public function __construct(string $message = '아직 구현되지 않은 기능입니다.', array $meta = [])
    {
        parent::__construct($message, 501, $meta);
    }
}