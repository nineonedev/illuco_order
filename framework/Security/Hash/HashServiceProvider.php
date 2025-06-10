<?php

namespace Framework\Security\Hash;

use Framework\Core\Application;
use Framework\Core\ServiceProvider;
use Framework\Security\Hash\Contracts\HasherInterface; 


class HashServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(BcryptHasher::class, fn() => new BcryptHasher());

        $this->app->singleton(HasherInterface::class, function(Application $app){
            return $app->make(BcryptHasher::class);
        });
    }
}