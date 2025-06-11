<?php

namespace Framework\Security\Session;

use Framework\Security\Session\Contracts\SessionInterface;

class FlashBag
{
    protected SessionInterface $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;

        if (!$this->session->has('_flash')) {
            $this->session->set('_flash', [
                'old' => [],
                'new' => [],
            ]);
        }
    }

    public function get(string $key, $default = null)
    {
        return $this->session->get($key, $default);
    }

    public function has(string $key): bool
    {
        return $this->session->has($key);
    }

    public function setMany(array $values): void
    {
        foreach ($values as $key => $value) {
            $this->set($key, $value);
        }
    }

    public function set(string $key, $value): void
    {
        $this->session->set($key, $value);

        $flash = $this->session->get('_flash', ['new' => [], 'old' => []]);
        $flash['new'][] = $key;
        $this->session->set('_flash', $flash);
    }

    public function all(): array
    {
        $keys = array_merge(
            $this->session->get('_flash')['old'] ?? [],
            $this->session->get('_flash')['new'] ?? []
        );

        $result = [];
        foreach ($keys as $key) {
            $result[$key] = $this->session->get($key);
        }

        return $result;
    }

    public function rotate(): void
    {
        $flash = $this->session->get('_flash', ['new' => [], 'old' => []]);

        foreach ($flash['old'] as $key) {
            $this->session->forget($key);
        }

        $flash['old'] = $flash['new'] ?? [];
        $flash['new'] = [];

        $this->session->set('_flash', $flash);
    }

    public function keep(string $key): void
    {
        $flash = $this->session->get('_flash', ['old' => [], 'new' => []]);

        if (in_array($key, $flash['old'], true)) {
            $flash['new'][] = $key;
            $this->session->set('_flash', $flash);
        }
    }

    public function reflash(): void
    {
        $flash = $this->session->get('_flash', ['old' => [], 'new' => []]);

        $flash['new'] = array_merge($flash['new'], $flash['old']);
        $this->session->set('_flash', $flash);
    }

    public function setInput(array $input): void
    {
        $this->set('_old_input', $input);
    }

    public function setErrors(array $errors): void
    {
        $this->set('_errors', $errors);
    }

    public function getInput(string $key = null, $default = null)
    {
        $input = $this->get('_old_input', []);
        return $key ? ($input[$key] ?? $default) : $input;
    }

    public function getErrors(): array
    {
        return $this->get('_errors', []);
    }

    public function hasErrors(): bool
    {
        return $this->has('_errors') && !empty($this->get('_errors'));
    }
}
