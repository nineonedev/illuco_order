<?php

namespace Framework\Security\Auth;

use Framework\Security\Auth\Contracts\GateInterface;
use Framework\Support\Exceptions\Http\AuthorizationException;
use InvalidArgumentException;

class GateManager implements GateInterface
{
    /** @var array<string, callable> */
    protected array $abilities = [];

    /** @var array<string, class-string> */
    protected array $policies = [];

    public function define(string $ability, callable $callback): void
    {
        $this->abilities[$ability] = $callback;
    }

    public function allows(string $ability, array $arguments = []): bool
    {
        $this->resolvePolicyIfNeeded($ability, $arguments);

        if (!isset($this->abilities[$ability])) {
            throw new InvalidArgumentException("Ability [$ability] is not defined.");
        }

        return (bool) call_user_func($this->abilities[$ability], ...$arguments);
    }

    public function denies(string $ability, array $arguments = []): bool
    {
        return !$this->allows($ability, $arguments);
    }

    public function authorize(string $ability, array $arguments = []): bool
    {
        return $this->allows($ability, $arguments);
    }

    public function authorizeOrFail(string $ability, array $arguments = []): void
    {
        if (!$this->authorize($ability, $arguments)) {
            throw new AuthorizationException();
        }
    }

    public function policy(string $class, string $policyClass): void
    {
        $this->policies[$class] = $policyClass;
    }

    protected function resolvePolicyIfNeeded(string $ability, array $arguments): void
    {
        if (isset($this->abilities[$ability]) || empty($arguments)) {
            return;
        }

        $target = $arguments[0];
        $class = is_object($target) ? get_class($target) : $target;

        if (!isset($this->policies[$class])) {
            return;
        }

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