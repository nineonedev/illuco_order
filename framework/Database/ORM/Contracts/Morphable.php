<?php

namespace Framework\Database\ORM\Contracts; 

interface Morphable
{
    public static function morphType(): string;

}