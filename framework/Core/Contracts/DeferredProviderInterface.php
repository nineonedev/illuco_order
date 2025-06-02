<?php 

namespace Framework\Core\Contracts; 

interface DeferredProviderInterface
{
    public function provides(): array;
}