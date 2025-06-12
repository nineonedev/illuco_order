<?php

namespace Framework\Database\ORM\Entities; 

interface Permissionable
{
    public static function permissionType(): string;
}