<?php

namespace Framework\Http;

use Framework\Core\Application;

class Pipeline
{
    protected array $middlewares = [];

    /** @var mixed */
    protected $passable;

    /** @var callable */
    protected $destination;

    protected string $method = 'handle';

    protected Application $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function via(string $method): self
    {
        $this->method = $method;
        return $this;
    }

    public function send($passable): self
    {
        $this->passable = $passable;
        return $this;
    }

    /**
     * @param array|string $middlewares
     */
    public function through($middlewares): self
    {
        $this->middlewares = is_array($middlewares) ? $middlewares : [$middlewares];
        return $this;
    }

    public function then(callable $destination)
    {
        $this->destination = $destination;

        $pipeline = array_reduce(
            array_reverse($this->middlewares),
            $this->carry(),
            $this->prepareDestination()
        );

        return $pipeline($this->passable);
    }

    protected function carry(): callable
    {
        return function ($stack, $middleware) {
            return function ($passable) use ($stack, $middleware) {
                $instance = is_object($middleware)
                    ? $middleware
                    : $this->app->make($middleware);

                if (!method_exists($instance, $this->method)) {
                    throw new \RuntimeException("Middleware must have a {$this->method} method.");
                }

                return $instance->{$this->method}($passable, $stack);
            };
        };
    }

    protected function prepareDestination(): callable
    {
        return function ($passable) {
            return call_user_func($this->destination, $passable);
        };
    }
}
