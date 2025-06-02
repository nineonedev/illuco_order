<?php

use Framework\Core\Application;

if (!function_exists('app')) {
    function app(?string $abstract = null)
    {
        $app = Application::getInstance();

        if ($abstract) {
            return $app->make($abstract);
        } 

        return $app;
    }
}