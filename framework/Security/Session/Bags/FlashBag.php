<?php

namespace Framework\Security\Session\Bags;

use Framework\Security\Session\SessionStore;

class FlashBag
{
    protected const FLASH_OLD = '_flash_old';
    protected const FLASH_NEW = '_flash_new';
    const ERROR_KEY = '_errors';
    const INPUT_KEY = '_input';

    protected SessionStore $store;

    public function __construct(SessionStore $store)
    {
        $this->store = $store;
    }

    public function set(string $key, $value): void
    {
        $this->store->set($key, $value);
        $this->addToNew($key);
    }

    public function setMany(array $items): void
    {
        foreach ($items as $key => $value) {
            $this->set($key, $value);
        }
    }

    public function now(string $key, $value): void
    {
        $this->store->set($key, $value);
    }

    public function get(string $key, $default = null)
    {
        return $this->store->get($key, $default);
    }

    public function keep($keys): void
    {
        $keys = is_array($keys) ? $keys : [$keys];

        foreach ($keys as $key) {
            $this->addToNew($key);
        }
    }

    public function reflash(): void
    {
        $this->keep($this->old());
    }

    public function sweep(): void
    {
        foreach ($this->old() as $key) {
            $this->store->forget($key);
        }

        $this->store->setMany([
            self::FLASH_OLD => $this->new(),
            self::FLASH_NEW => [],
        ]);
    }

    protected function old(): array
    {
        return $this->store->get(self::FLASH_OLD, []);
    }

    protected function new(): array
    {
        return $this->store->get(self::FLASH_NEW, []);
    }

    protected function addToNew(string $key): void
    {
        $new = $this->new();

        if (!in_array($key, $new, true)) {
            $new[] = $key;
            $this->store->set(self::FLASH_NEW, $new);
        }
    }

    public function setInput(array $input): void
    {
        $this->set(self::INPUT_KEY, $input);
    }

    public function setErrors(array $errors): void
    {
        $this->set(self::ERROR_KEY, $errors);
    }
}