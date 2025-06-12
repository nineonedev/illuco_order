<?php 

namespace Framework\Support\Exceptions\Http;

use Framework\Support\Exceptions\BaseException;

class BadRequestException extends BaseException
{
    public function __construct(string $message = '잘못된 요청입니다.', array $meta = [])
    {
        parent::__construct($message, 400, $meta);
    }
}