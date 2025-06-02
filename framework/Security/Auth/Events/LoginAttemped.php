<?php

namespace Framework\Security\Auth\Events;

use Framework\Bus\Contracts\EventInterface;

class LoginAttemped implements EventInterface
{
    public $user; 
    public $credentials; 

    public function __construct($user, array $credentials)
    {
        $this->user = $user; 
        $this->credentials = $credentials; 
    }

    public function name(): string
    {
        return static::class;
    }
}