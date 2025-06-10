<?php

namespace Framework\View\Contracts;

interface ViewFinderInterface
{
    public function find(string $view): string;
    public function exists(string $view): bool;
    public function getBasePath(): string;
}
