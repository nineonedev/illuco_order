<?php

namespace Framework\Support\Exceptions\Http;

use Framework\Support\Exceptions\BaseException;

class UnauthenticatedException extends BaseException
{
    public function __construct(?string $message = null, array $meta = [])
    {
        parent::__construct($message ?? lang('validation.unauthenticated'), 401, $meta);
    }
}