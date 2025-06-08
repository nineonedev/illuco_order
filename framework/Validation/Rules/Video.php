<?php

namespace Framework\Validation\Rules;

class Video extends MimeType
{
    public function __construct()
    {
        parent::__construct('video');
    }

    public function message(): string
    {
        return lang('rule.video');
    }
}
