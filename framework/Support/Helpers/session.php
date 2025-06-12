<?php 

use Framework\Security\Session\Contracts\SessionInterface;
use Framework\Security\Session\SessionManager;


if (!function_exists('session')) {
    function session(): SessionManager
    {
        return app(SessionManager::class);
    }
}


if (!function_exists('session_driver')) {
    function session_driver($name = null): SessionInterface
    {
        return session()->driver($name);
    }
}

if (!function_exists('flash')) {
    function flash($key = null, $value = null)
    {
        $flashBag = session()->flashBag();

        if ($key === null) {
            return $flashBag;
        }

        if (is_array($key)) {
            $flashBag->setMany($key);
            return;
        }

        $flashBag->set($key, $value);
    }
}


if (!function_exists('errors')) {
    function errors(): array
    {
       return flash()->getErrors();
    }
}

if (!function_exists('old')) {
    function old(string $key, $default = null)
    {
       return flash()->getInput($key, $default);
    }
}
