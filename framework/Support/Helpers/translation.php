<?php

use Framework\Database\TransactionManager;
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

if (!function_exists('transfer')) {
    /**
     * @example transfer('rule.between', 'system.age', [10, 20])
     * '{0}은 {1}~{2} 사이여야 합니다.' → "나이는 10~20 사이여야 합니다."
     */
    function transfer(string $key, string $labelKey, array $replace = [], ?string $locale = null): ?string
    {
        // 라벨 값 얻기
        $label = lang($labelKey, [], $locale);
        // 첫번째 자리에 라벨 끼워 넣음 (순서 치환)
        array_unshift($replace, $label);
        return lang($key, $replace, $locale);
    }
}

if (!function_exists('translator')) {
    function translator(): TranslatorManager
    {
        return app(TransactionManager::class);
    }
}


if (!function_exists('_')) {
    function _(string $key, array $replace = [], ?string $locale = null): ?string
    {
        return translator()->getDefaultTranslator()->get($key, $replace, $locale);
    }
}

if (!function_exists('get_locale')) {
    function get_locale(): string
    {
        return app()->getLocale();
    }
}

if (!function_exists('set_locale')) {
    function set_locale(string $locale): void
    {
        app()->setLocale($locale);
    }
}

if (!function_exists('get_fallback_locale')) {
    function get_fallback_locale(): string
    {
        return translator()->getFallback();
    }
}

if (!function_exists('set_fallback_locale')) {
    function set_fallback_locale(string $locale): void
    {
        translator()->setFallback($locale);
    }
}
