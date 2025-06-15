<?php

namespace Framework\Security\Auth\Access;

trait Authorizable
{
    public function can(string $ability, ...$arguments): bool
    {
        return gate()->allows($ability, [$this, ...$arguments]);
    }

    public function cannot(string $ability, ...$arguments): bool
    {
        return !$this->can($ability, ...$arguments);
    }

    public function authorize(string $ability, ...$arguments): void
    {
        gate()->authorizeOrFail($ability, [$this, ...$arguments]);
    }
}