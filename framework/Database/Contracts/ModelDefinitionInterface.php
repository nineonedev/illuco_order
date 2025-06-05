<?php

namespace Framework\Database\Contracts; 

interface ModelDefinitionInterface
{
    public function entityClass(): string;

    public function repositoryClass(): string; 

    public function defineRelations(): array;
}