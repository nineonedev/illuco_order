<?php

namespace Framework\Validation;

use Framework\Validation\Rules\Rule;

class Validator
{
    protected array $data = [];
    protected array $rules = [];
    protected array $messages = [];
    protected array $errors = [];

    public function __construct(array $data = [])
    {
        $this->data = $data;
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
                $ruleInstance = $this->createRuleInstance($rule['name'], $rule['params']);
                if (!$ruleInstance->passes($this->data[$field] ?? null)) {
                    $this->errors[$field] = $this->messages[$field] ?? $ruleInstance->message();
                }
            }
        }

        return empty($this->errors);
    }

    /**
     * Return only the validated (passed) data.
     *
     * @return array
     */
    public function validated(): array
    {
        // 유효성 실패한 필드 제외하고 반환
        return array_filter(
            $this->data,
            fn ($key) => !array_key_exists($key, $this->errors),
            ARRAY_FILTER_USE_KEY
        );
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
     * Create a rule instance.
     *
     * @param string $rule
     * @param array $params
     * @return Rule
     */
    protected function createRuleInstance(string $rule, array $params = []): Rule
    {
        $ruleClass = "Framework\\Validation\\Rules\\" . ucfirst($rule);

        if (!class_exists($ruleClass)) {
            throw new \RuntimeException("Rule {$rule} does not exist.");
        }

        return new $ruleClass(...$params);
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
        $validator = new self($data);

        foreach ($rules as $field => $ruleSet) {
            $validator->addRule($field, $ruleSet);
        }

        return $validator;
    }
}
