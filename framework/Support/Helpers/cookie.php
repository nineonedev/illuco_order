<?php 

use Framework\Security\Cookie\CookieManager;



if (!function_exists('cookie')) {
    function cookie(): CookieManager
    {
        return app(CookieManager::class);
    }
}
