<?php

namespace Framework\Validation\Rules;

class Image extends MimeType
{
    public function __construct()
    {
        parent::__construct('image');
    }

    public function message(): string
    {
        return lang('rule.image'); // 에러 메시지 별도 정의해도 됨
    }
}
