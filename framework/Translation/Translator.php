<?php

namespace Framework\Translation;

use Framework\Translation\Contracts\LoaderInterface;
use Framework\Translation\Contracts\TranslatorInterface;
use Framework\Support\Arr;

class Translator implements TranslatorInterface
{
    protected string $locale;
    protected string $fallback;
    protected LoaderInterface $loader;

    public function __construct(string $locale, string $fallback, LoaderInterface $loader)
    {
        $this->locale = $locale;
        $this->fallback = $fallback;
        $this->loader = $loader;
    }

    public function get(string $key, array $replace = [], ?string $locale = null): ?string
    {
        $locale = $locale ?: $this->locale;

        $lines = $this->loader->load($locale);
        $value = Arr::get($lines, $key);

        if ($value === null && $this->fallback !== $locale) {
            $fallbackLines = $this->loader->load($this->fallback);
            $value = Arr::get($fallbackLines, $key);
        }

        if (!is_string($value)) return $value;

        foreach ($replace as $i => $v) {
            $value = str_replace('{' . $i . '}', $v, $value);   // {0}, {1} 치환
        }

        foreach ($replace as $k => $v) {
            $value = str_replace(':' . $k, $v, $value);
        }

        return $value;
    }

    public function has(string $key, ?string $locale = null): bool
    {
        $locale = $locale ?: $this->locale;

        $lines = $this->loader->load($locale);

        return Arr::has($lines, $key);
    }

    public function getLocale(): string
    {
        return $this->locale;
    }

    public function setLocale(string $locale): void
    {
        $this->locale = $locale;
    }

    public function getFallback(): string
    {
        return $this->fallback;
    }

    public function setFallback(string $locale): void
    {
        $this->fallback = $locale;
    }
}
