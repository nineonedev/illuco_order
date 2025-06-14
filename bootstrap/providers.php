<?php

use App\Providers\AppServiceProvider;
use Framework\Bus\BusServiceProvider;
use Framework\Database\DatabaseServiceProvider;
use Framework\Filesystem\FilesystemServiceProvider;
use Framework\Http\HttpServiceProvider;
use Framework\Security\Auth\AuthServiceProvider;
use Framework\Security\Cookie\CookieServiceProvider;
use Framework\Security\Csrf\CsrfServiceProvider;
use Framework\Security\Encryption\EncryptionServiceProvider;
use Framework\Security\Hash\HashServiceProvider;
use Framework\Security\Session\SessionServiceProvider;
use Framework\View\ViewServiceProvider;

return [
    // ==========================================================================
    // Core
    // ==========================================================================
    HttpServiceProvider::class,
    HashServiceProvider::class,
    EncryptionServiceProvider::class,
    SessionServiceProvider::class,

    CsrfServiceProvider::class,
    CookieServiceProvider::class,
    AuthServiceProvider::class,
    FilesystemServiceProvider::class,

    DatabaseServiceProvider::class,
    BusServiceProvider::class,
    ViewServiceProvider::class,

    // ==========================================================================
    // Application
    // ==========================================================================
    AppServiceProvider::class,
];