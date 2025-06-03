<?php

namespace Framework\Database\Contracts;

interface CastInterface
{
    /**
     * DB => PHP
     */
    public function cast($value);

    /**
     * PHP => DB
     */
    public function recast($value);
}
