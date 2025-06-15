<?php

namespace Framework\Support\Exceptions\Http;

use Framework\Support\Exceptions\BaseException;

class AuthorizationException extends BaseException
{
    public function __construct(?string $message = null, array $meta = [])
    {
        parent::__construct($message ?? lang('validation.authorization'), 403, $meta);
    }
}
