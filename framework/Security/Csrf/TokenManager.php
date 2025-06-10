<?php

namespace Framework\Security\Csrf;

use Framework\Constants\AuthConstants;
use Framework\Security\Session\Contracts\SessionInterface;

class TokenManager implements TokenManagerInterface
{
    public SessionInterface $session; 

    public function __construct(SessionInterface $session)
    {
        $this->session = $session; 
    }

    public function generate(): string
    {
        $token = bin2hex(random_bytes(32)); 
        $this->session->set(AuthConstants::CSRF_TOKEN_KEY, $token); 
        return $token; 
    }

    public function token(): ?string
    {
        return $this->session->get(AuthConstants::CSRF_TOKEN_KEY); 
    }

    public function verify(string $token): bool
    {
        $stored = $this->token(); 
        return is_string($stored) && hash_equals($stored, $token); 
    }

    public function field(): string
    {
        return '<input type="hidden" name="'.AuthConstants::CSRF_TOKEN_KEY.'" value="'.$this->generate() . '"/>';
    }
}