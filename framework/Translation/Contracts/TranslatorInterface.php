<?php

namespace Framework\Translation\Contracts;

interface TranslatorInterface
{
    /**
     * @return mixed
     */
    public function get(string $key, array $replace = [], ?string $locale = null);

    public function has(string $key, ?string $locale = null): bool;

    public function setLocale(string $locale): void;

    public function getLocale(): string;

    public function getFallback(): string;

    public function setFallback(string $locale): void;
}
