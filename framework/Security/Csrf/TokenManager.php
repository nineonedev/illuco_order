<?php

namespace Framework\Security\Csrf;

use Framework\Security\Contracts\SessionInterface;
use Framework\Security\Contracts\TokenManagerInterface;

class TokenManager implements TokenManagerInterface
{
    const KEY = '_csrf_token'; 
    public SessionInterface $session; 

    public function __construct(SessionInterface $session)
    {
        $this->session = $session; 
    }

    public function generate(): string
    {
        $token = bin2hex(random_bytes(32)); 
        $this->session->set(self::KEY, $token); 

        return $token; 
    }

    public function token(): ?string
    {
        return $this->session->get(self::KEY); 
    }

    public function verify(string $token): bool
    {
        $stored = $this->token(); 
        return is_string($stored) && hash_equals($stored, $token); 
    }

    public function field(): string
    {
        return '<input type="hidden" name="'.static::KEY.'" value="'.$this->generate() . '"/>';
    }
}