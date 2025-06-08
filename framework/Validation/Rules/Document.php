<?php

namespace Framework\Validation\Rules;

class Document extends MimeType
{
    public function __construct()
    {
        parent::__construct('document');
    }

    public function message(): string
    {
        return lang('rule.document');
    }
}
