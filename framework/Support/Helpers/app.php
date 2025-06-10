<?php

use Framework\Core\Application;

if (!function_exists('app')) {
    function app(?string $abstract = null, array $parameters = [])
    {
        $app = Application::getInstance();

        if ($abstract) {
            return $app->make($abstract, $parameters);
        } 

        return $app;
    }
}

