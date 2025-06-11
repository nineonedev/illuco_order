<?php

use Framework\Core\Application;
use Framework\Core\Contracts\KernelInterface;
use Framework\View\ViewEngine;

return [
    'app' => Application::class,
    'view' => ViewEngine::class,
    'kernel' => KernelInterface::class,
];