<?php

namespace Framework\Database\Model\Casts;

interface CastInterface
{
    public function set($value);   // DB에 저장하기 위한 변환
    public function get($value);   // DB에서 가져온 값을 PHP에서 사용하기 위한 변환
}
