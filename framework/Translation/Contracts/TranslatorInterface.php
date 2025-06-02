<?php

namespace Framework\Translation\Contracts;

interface TranslatorInterface
{
    public function get(string $key, array $replace = [], ?string $locale = null): ?string;

    public function has(string $key, ?string $locale = null): bool;

    public function setLocale(string $locale): void;

    public function getLocale(): string;

    public function getFallback(): string;

    public function setFallback(string $locale): void;
}
