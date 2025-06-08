<?php

namespace Framework\Security\Auth\Providers; 

interface AuthenticatableInterface 
{
    /**
     * @return mixed
     */
    public function getAuthIdentifier();

    public function getAuthPassword(): string;
}