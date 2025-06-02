<?php 

namespace Framework\Http;

use Framework\Core\Application;
use Framework\Core\ServiceProvider;

class HttpServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(Response::class, Response::class); 

        $this->app->bind(Redirector::class, fn () => new Redirector()); 

        $this->app->bind(Pipeline::class, fn(Application $app) => new Pipeline($app));
    }
}