<?php

namespace Framework\Validation\Rules;

class Archive extends MimeType
{
    public function __construct()
    {
        parent::__construct('archive');
    }

    public function message(): string
    {
        return lang('rule.archive');
    }
}
