<?php

use Framework\Bus\BusServiceProvider;
use Framework\Database\DatabaseServiceProvider;
use Framework\Filesystem\FilesystemServiceProvider;
use Framework\Http\HttpServiceProvider;
use Framework\Security\SecurityServiceProvider;
use Framework\Translation\TranslationServiceProvider;
use Framework\View\ViewServiceProvider;

return [
    HttpServiceProvider::class,
    SecurityServiceProvider::class,
    TranslationServiceProvider::class,
    FilesystemServiceProvider::class,
    DatabaseServiceProvider::class,
    ViewServiceProvider::class,
    BusServiceProvider::class,
];