<?php

use Framework\State\Config;
use Framework\State\Env;


if (!function_exists('env')) {
    /**
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    function env(string $key, $default = null)
    {
        /** @var Env $env */
        $env = app(Env::class);
        return $env->get($key, $default); 
    }
}

if (!function_exists('config')) {
    /**
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    function config(string $key, $default = null)
    {
        /** @var Config $config */
        $config = app(Config::class);
        return $config->get($key, $default); 
    }
}