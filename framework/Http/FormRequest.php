<?php 

namespace Framework\Http;

use Framework\Support\Exceptions\ValidationException; 

abstract class FormRequest extends Request {
    public function __construct(
        array $get = [], 
        array $post = [], 
        array $cookies = [], 
        array $files = [],
        array $server = []
    )
    {
        parent::__construct($get, $post, $cookies, $files, $server);

        if (!$this->authorize()) {
            throw new ValidationException("This action is unauthorized.");
        }

        foreach ($this->messages() as $field => $message) {
            $this->validator->addMessage($field, $message); 
        }
    }

    abstract protected function rules(): array;

    protected function messages(): array
    {
        return [];
    }

    protected function authorize(): bool
    {
        return true;
    }
}