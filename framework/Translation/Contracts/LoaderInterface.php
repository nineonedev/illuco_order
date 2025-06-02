<?php

namespace Framework\Translation\Contracts; 

interface LoaderInterface
{
    public function load(string $locale): array; 
    
    public function getPath(): string;

    public function setPath(string $path): void; 
}