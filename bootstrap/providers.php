<?php

use Framework\Bus\BusServiceProvider;
use Framework\Database\DatabaseServiceProvider;
use Framework\Filesystem\FilesystemServiceProvider;
use Framework\Http\HttpServiceProvider;
use Framework\Security\SecurityServiceprovider;
use Framework\Translation\TranslationServiceProvider;
use Framework\View\ViewServiceProvider;

return [
    HttpServiceProvider::class,
    SecurityServiceprovider::class,
    TranslationServiceProvider::class,
    FilesystemServiceProvider::class,
    DatabaseServiceProvider::class,
    ViewServiceProvider::class,
    BusServiceProvider::class,
];