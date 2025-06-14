<?php

namespace Framework\Validation;

use Framework\Support\Exceptions\ValidationException;

class Validator
{
    protected array $data = [];
    protected array $rules = [];
    protected array $messages = [];
    protected array $errors = [];
    protected array $validatedData = [];

    public function __construct(array $data = [], array $rules = [])
    {
        $this->data = $data;
        $this->rules = $rules; 

        $this->addRules($rules);
    }

    /**
     * Add a rule to the validator.
     *
     * @param string $field
     * @param string|array $rules
     * @return $this
     */
    public function addRule(string $field, $rules): self
    {
        if (is_string($rules)) {
            $rules = $this->parseRules($rules);
        }

        $this->rules[$field] = $rules;

        return $this;
    }

    public function addRules(array $rules): void
    {
        foreach ($rules as $field => $ruleSet) {
            $this->addRule($field, $ruleSet);
        }
    }

    /**
     * Add custom error messages.
     *
     * @param string $field
     * @param string $message
     * @return $this
     */
    public function addMessage(string $field, string $message): self
    {
        $this->messages[$field] = $message;
        return $this;
    }


    /**
     * Validate the data.
     *
     * @return bool
     */
    public function validate(): bool
    {
        foreach ($this->rules as $field => $rules) {
            foreach ($rules as $rule) {
                $ruleInstance = RuleFactory::create($rule['name'], $rule['params']);
                $ruleInstance->setField($field);

                if (!$ruleInstance->passes($this->data[$field] ?? null)) {
                    $this->errors[$field] = $this->messages[$field] ?? $ruleInstance->message();
                }
            }
        }

        return empty($this->errors);
    }

    public function setValidated(array $validated): void
    {
        $this->validatedData = array_merge($this->validated(), $validated);
    }

    /**
     * Return only the validated (passed) data.
     *
     * @return array
     */
    public function validated(): array
    {
        return $this->validatedData ?: array_filter(
            $this->data,
            fn ($key) => !array_key_exists($key, $this->errors),
            ARRAY_FILTER_USE_KEY
        );
    }

    /**
     * Validate and throw exception on fail.
     *
     * @throws ValidationException
     */
    public function validateOrFail(): bool
    {
        if (!$this->validate()) {
            throw new ValidationException(lang('system.validation.failed'), $this->errors);
        }
        
        return true;
    }

    /**
     * Reset the validator's state.
     */
    public function flush(): void
    {
        $this->errors = [];
        $this->messages = [];
        $this->rules = [];
        // $this->data = []; // data는 유지할지 선택 (보통 유지)
    }

    /**
     * Check if validation fails.
     *
     * @return bool
     */
    public function fails(): bool
    {
        return !$this->validate();
    }

    /**
     * Get validation errors.
     *
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }


    /**
     * Parse the rule string into individual rules and their parameters.
     *
     * @param string $rules
     * @return array
     */
    protected function parseRules(string $rules): array
    {
        $parsedRules = [];
        $rulesArray = explode('|', $rules);

        foreach ($rulesArray as $rule) {
            $parts = explode(':', $rule);
            $name = $parts[0];
            $params = isset($parts[1]) ? explode(',', $parts[1]) : [];

            $parsedRules[] = ['name' => $name, 'params' => $params];
        }

        return $parsedRules;
    }

    /**
     * Static method to make a new Validator instance.
     *
     * @param array $data
     * @param array $rules
     * @return Validator
     */
    public static function make(array $data, array $rules): Validator
    {
        $validator = new self($data, $rules);
        return $validator;
    }
}
