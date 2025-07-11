<?php

namespace Framework\Translation;

use Framework\Support\Str;
use Framework\Translation\Contracts\TranslatorInterface;

class TranslatorManager implements TranslatorInterface
{
    protected string $locale;
    protected string $fallback;
    protected string $default;

    /**
     * @var array<string, TranslatorInterface>
     */
    protected array $translators = [];

    public function __construct(string $locale, string $fallback, string $default = 'default')
    {
        $this->locale = $locale;
        $this->fallback = $fallback;
        $this->default = $default;
    }

    public function getDefaultTranslator(): TranslatorInterface
    {
        return $this->translators[$this->default];
    }

    public function addTranslator(string $name, TranslatorInterface $translator): void
    {
        $this->translators[$name] = $translator;
    }

    public function getTranslator(string $name): ?TranslatorInterface
    {
        return $this->translators[$name] ?? null;
    }

    public function get(string $key, array $replace = [], ?string $locale = null)
    {
        [$group, $item] = $this->parseKey($key);
        $translator = $this->getTranslator($group);

        if (!$translator) {
            return null;
        }
        
        return $translator->get($item, $replace, $locale ?? $this->locale);
    }

    public function has(string $key, ?string $locale = null): bool
    {
        [$group, $item] = $this->parseKey($key);
        $translator = $this->getTranslator($group);

        if (!$translator) {
            return false; 
        }

        return $translator->has($item, $locale ?? $this->locale);
    }

    public function getLocale(): string
    {
        return $this->locale;
    }

    public function setLocale(string $locale): void
    {
        $this->locale = $locale;

        foreach ($this->translators as $translator) {
            $translator->setLocale($locale);
        }
    }

    public function getFallback(): string
    {
        return $this->fallback;
    }

    public function setFallback(string $locale): void
    {
        $this->fallback = $locale;

        foreach ($this->translators as $translator) {
            $translator->setFallback($locale);
        }
    }

    protected function parseKey(string $key): array
    {
        if (!Str::contains($key, '.')) {
            return [$this->default, $key];
        }

        [$group, $item] = explode('.', $key, 2);

        if (!isset($this->translators[$group])) {
            return [$this->default, $key];
        }

        return [$group, $item];
    }
}
