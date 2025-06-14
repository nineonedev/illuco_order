<?php

namespace Framework\Support\Exceptions\Http;

use Framework\Support\Exceptions\BaseException;

class UnauthorizedException extends BaseException
{
    public function __construct(?string $message = null, array $meta = [])
    {
        parent::__construct($message ?? lang('validation.unauthorized'), 401, $meta);
    }
}
