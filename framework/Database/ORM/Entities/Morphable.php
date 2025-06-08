<?php 

namespace Framework\Database\ORM\Entities; 

interface Morphable 
{
    public static function morphType(): string;
}