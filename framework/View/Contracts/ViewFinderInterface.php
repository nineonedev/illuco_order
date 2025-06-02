<?php

namespace Framework\View\Contracts;

interface ViewFinderInterface
{
    public function find(string $view): string;

    public function getBasePath(): string;
}
