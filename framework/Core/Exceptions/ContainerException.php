<?php

namespace Framework\Core\Exceptions;

use Framework\Support\Exceptions\Http\InternalServerErrorException;

class ContainerException extends InternalServerErrorException
{
    public function __construct(string $message = 'Container error', array $meta = [])
    {
        parent::__construct($message, $meta);
    }
}
