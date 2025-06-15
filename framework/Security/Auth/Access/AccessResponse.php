<?php

namespace Framework\Security\Auth\Access; 

class AccessResponse
{
    protected bool $allowed;
    protected ?string $message;

    public function __construct(bool $allowed, ?string $message = null)
    {
        $this->allowed = $allowed;
        $this->message = $message;
    }

    public static function allow(): self
    {
        return new self(true);
    }

    public static function deny(string $message): self
    {
        return new self(false, $message);
    }

    public function allowed(): bool
    {
        return $this->allowed;
    }

    public function message(): ?string
    {
        return $this->message;
    }
}
