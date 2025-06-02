<?php 

namespace Framework\Core; 

class ServiceProvider 
{
    protected Application $app; 

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function register(): void
    {}

    public function boot(): void
    {}

    public function before(): void
    {}

    public function after(): void
    {}
}