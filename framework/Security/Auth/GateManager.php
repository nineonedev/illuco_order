<?php

namespace Framework\Security\Auth;

use Framework\Security\Auth\Contracts\GateInterface;
use InvalidArgumentException;

class GateManager implements GateInterface
{
    /** @var array<string,callable> */
    protected array $abilities = [];

    protected array $policies = [];

    public function define(string $ability, callable $callback): void
    {
        $this->abilities[$ability] = $callback;
    }

    public function allows(string $ability, array $arguments = []): bool
    {
        if (empty($this->abilities[$ability]) && count($arguments) > 0) {
            $target = $arguments[0];
            $class = is_object($target) ? get_class($target) : $target;

            if (isset($this->policies[$class])) {
                $policyClass = $this->policies[$class];
                $policy = app($policyClass);

                if (!method_exists($policy, $ability)) {
                    throw new \RuntimeException("Policy [{$policyClass}] does not define method [$ability].");
                }

                $this->define($ability, function ($user, ...$args) use ($policy, $ability) {
                    return $policy->$ability($user, ...$args);
                });
            }
        }

        if (!isset($this->abilities[$ability])) {
            throw new InvalidArgumentException("Ability [$ability] is not defined."); 
        }

        return (bool) call_user_func_array($this->abilities[$ability], $arguments); 
    }


    public function defines(string $ability, array $arguments = []): bool
    {
        return !$this->allows($ability, $arguments);
    }

    public function policy(string $class, string $policyClass): void
    {
        $this->policies[$class] = $policyClass;
    }
}