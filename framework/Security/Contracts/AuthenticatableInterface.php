<?php

namespace Framework\Security\Contracts; 

interface AuthenticatableInterface 
{
    /**
     * @return mixed
     */
    public function getAuthIdentifier();

    public function getAuthPassword(): string;
}