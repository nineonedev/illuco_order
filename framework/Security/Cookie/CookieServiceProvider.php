<?php 

namespace Framework\Security\Cookie;

use Framework\Core\ServiceProvider;
use Framework\Security\Cookie\CookieManager;

class CookieServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        
        $this->app->singleton(CookieManager::class, function(){
            return new CookieManager(); 
        });
    }
}