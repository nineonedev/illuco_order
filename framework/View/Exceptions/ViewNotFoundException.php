<?php

namespace Framework\View\Exceptions;

use Framework\Support\Exceptions\Http\InternalServerErrorException;

class ViewNotFoundException extends InternalServerErrorException
{
    public function __construct(string $message = 'Cannot found view', array $meta = [])
    {
        parent::__construct($message, $meta);
    }
}
