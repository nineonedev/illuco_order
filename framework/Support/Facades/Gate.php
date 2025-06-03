<?php

namespace Framework\Support\Facades;

use Framework\Security\Auth\GateManager;

/**
 * @var GateManager Gate
 */
class Gate extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return '';
    }
}