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

if (!function_exists('storage_path')) {
    function storage_path($path)
    {
        $app = Application::getInstance();
        return $app->storagePath() . '/' . ltrim($path, '/');
    }
}

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
