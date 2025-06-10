<?php

use Framework\Support\Facades\View;
use Framework\View\ViewRenderer;

if (!function_exists('view')) {
    /**
     * @return ViewRenderer|string
     */
    function view(?string $view = null, array $data = [])
    {
        if ($view === null) {
            return app(ViewRenderer::class);
        }

        return View::render($view, $data);
    }
}

if (!function_exists('extend')) {
    function extend(string $layout): void
    {
        View::setLayout($layout);
    }
}

if (!function_exists('share')) {
    function share(string $key, $value): void
    {
        View::share($key, $value);
    }
}

if (!function_exists('shares')) {
    function shares(array $data): void
    {
        View::shares($data);
    }
}

if (!function_exists('shared')) {
    function shared(?string $key = null, $default = null)
    {
        return View::shared($key, $default);
    }
}

if (!function_exists('section')) {
    function section(string $name): void
    {
        View::section($name);
    }
}

if (!function_exists('end_section')) {
    function end_section(): void
    {
        View::endSection();
    }
}

if (!function_exists('yield_section')) {
    function yield_section(string $name, string $default = ''): string
    {
        return View::yield($name, $default);
    }
}

if (!function_exists('component')) {
    function component(string $view, array $data = []): void
    {
        View::component($view, $data);
    }
}

if (!function_exists('end_component')) {
    function end_component(): string
    {
        return View::endComponent();
    }
}

if (!function_exists('include_view')) {
    function include_view(string $view, array $data = []): string
    {
        return View::include($view, $data);
    }
}