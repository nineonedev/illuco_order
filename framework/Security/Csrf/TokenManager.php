<?php

namespace Framework\Security\Csrf;

use Framework\Security\Session\Contracts\SessionInterface;

class TokenManager implements TokenManagerInterface
{
    const CSRF_INPUT_NAME = '_csrf_token'; 

    public SessionInterface $session; 

    public function __construct(SessionInterface $session)
    {
        $this->session = $session; 
    }

    public function generate(): string
    {
        $token = bin2hex(random_bytes(32)); 
        $this->session->set(self::CSRF_INPUT_NAME, $token); 
        return $token; 
    }

    public function token(): ?string
    {
        return $this->session->get(self::CSRF_INPUT_NAME); 
    }

    public function verify(string $token): bool
    {
        $stored = $this->token(); 
        return is_string($stored) && hash_equals($stored, $token); 
    }

    public function field(): string
    {
        return '<input type="hidden" name="'.self::CSRF_INPUT_NAME.'" value="'.$this->generate() . '"/>';
    }
}