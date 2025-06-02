<?php

use Framework\Translation\TranslatorManager;

if (!function_exists('translation')) {
    function translation(string $key, array $replace = [], ?string $locale = null): ?string
    {
        /** @var TranslatorManager $manager */
        $manager = app()->make(TranslatorManager::class);
    
        return $manager->get($key, $replace, $locale);
    }
}

if (!function_exists('lang')) {
    function lang(string $key, array $replace = [], ?string $locale = null): ?string
    {
        return translation($key, $replace, $locale);
    }
}

if (!function_exists('_')) {
    function _(string $key, array $replace = [], ?string $locale = null): ?string
    {
        /** @var TranslatorManager $manager */
        $manager = app()->make(TranslatorManager::class);

        return $manager->getDefaultTranslator()->get($key, $replace, $locale);
    }
}

if (!function_exists('get_locale')) {
    function get_locale(): string
    {
        /** @var TranslatorManager $manager */
        $manager = app()->make(TranslatorManager::class);

        return $manager->getLocale();
    }
}

if (!function_exists('set_locale')) {
    function set_locale(string $locale): void
    {
        /** @var TranslatorManager $manager */
        $manager = app()->make(TranslatorManager::class);

        $manager->setLocale($locale);
    }
}

if (!function_exists('get_fallback_locale')) {
    function get_fallback_locale(): string
    {
        /** @var TranslatorManager $manager */
        $manager = app()->make(TranslatorManager::class);

        return $manager->getFallback();
    }
}

if (!function_exists('set_fallback_locale')) {
    function set_fallback_locale(string $locale): void
    {
        /** @var TranslatorManager $manager */
        $manager = app()->make(TranslatorManager::class);

        $manager->setFallback($locale);
    }
}
