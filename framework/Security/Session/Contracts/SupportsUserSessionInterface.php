<?php

namespace Framework\Security\Session\Contracts;

interface SupportsUserSessionInterface
{
    public function userId(): ?int;

    public function setUserId(?int $userId = null): void;
}
