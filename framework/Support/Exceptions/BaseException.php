<?php

namespace Framework\Support\Exceptions;

use Exception;

class BaseException extends Exception 
{
    protected array $context = []; 

    public function __construct(
        string $message = "", 
        int $code = 0, 
        array $context = [], 
        ?Exception $previous = null
    )
    {
        parent::__construct($message, $code, $previous); 
        $this->context = $context; 
    }

    public function context(): array
    {
        return $this->context;
    }
}