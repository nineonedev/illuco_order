<?php

namespace Framework\Database\Model\Entities\Casts;

class WonCast extends MoneyCast
{
    public function __construct()
    {
        parent::__construct('KRW');
    }
}
