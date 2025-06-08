<?php

namespace Framework\Validation\Rules;

class CompositeMimeType extends Rule
{
    protected $rules = [];

    /**
     * @param string $expression ex) "image,document|audio|video"
     */
    public function __construct($expression)
    {
        $groups = preg_split('/[|,]/', $expression);
        $groups = array_map('trim', $groups);
        $groups = array_filter($groups);

        $this->rules[] = new MimeType(...$groups);
    }

    public function passes($value): bool
    {
        foreach ($this->rules as $rule) {
            if ($rule->passes($value)) return true;
        }
        return false;
    }

    public function message(): string
    {
        return lang('rule.mime_type');
    }
}
