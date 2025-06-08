<?php

namespace Framework\Validation\Rules;

class Audio extends MimeType
{
    public function __construct()
    {
        parent::__construct('audio');
    }

    public function message(): string
    {
        return lang('rule.audio');
    }
}
