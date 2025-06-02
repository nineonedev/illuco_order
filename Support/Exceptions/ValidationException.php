<?php 

namespace Framework\Support\Exceptions; 

class ValidationException extends BaseException 
{
    protected array $errors = []; 

    public function __construct(
        array $errors,
        string $message = "Validation failed",
        int $code = 422
    )
    {
        parent::__construct($message, $code); 
        $this->errors = $errors; 
    }

    public function errors(): array
    {
        return $this->errors;
    }
}