<?php

namespace Framework\Security\Encryption;

use Framework\Core\Application;
use Framework\Core\ServiceProvider; 

class EncryptionServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(EncrypterInterface::class, function(){
            return new Encrypter(env('APP_KEY', config('app.key', 'changeme-32-byte-key!@#$%12345678'))); 
        });
        
        $this->app->singleton(StringCipher::class, function(Application $app){
            return new StringCipher($app->make(EncrypterInterface::class));
        });

    }
}