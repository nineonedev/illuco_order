<?php

namespace Framework\Support\Exceptions;

use Framework\Http\Response;

interface RendersExceptionInterface
{
    public function renderHtml(): ?Response;

    public function renderJson(bool $debug = false): ?Response;

    public function renderForCli(): void;
}
