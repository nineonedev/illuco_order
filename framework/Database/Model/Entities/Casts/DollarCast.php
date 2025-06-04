<?php

namespace Framework\Database\Model\Entities\Casts;

class DollarCast extends MoneyCast
{
    public function __construct()
    {
        parent::__construct('USD');
    }
}
