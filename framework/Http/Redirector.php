<?php

namespace Framework\Http;
use Framework\Http\Responses\RedirectResponse;

class Redirector
{
    public static function to(string $url, int $status = 302): RedirectResponse
    {
        return new RedirectResponse($url, $status);
    }

    public static function back(int $status = 302): RedirectResponse
    {
        $referer = $_SERVER['HTTP_REFERER'] ?? '/';
        return self::to($referer, $status);
    }

    public static function home(int $status = 302): RedirectResponse
    {
        return self::to('/', $status);
    }

    public static function with(string $key, $value): RedirectResponse
    {
        // 예: 리다이렉트 + 세션 플래시
        return self::back()->withSession($key, $value);
    }

    public static function withErrors(array $errors): RedirectResponse
    {
        return self::back()->withErrors($errors);
    }

    public static function withInput(array $input): RedirectResponse
    {
        return self::back()->withInput($input);
    }

    public static function previous(): string
    {
        return $_SERVER['HTTP_REFERER'] ?? '/';
    }

    public static function intended(string $default = '/', int $status = 302): RedirectResponse
    {
        $intended = $_SESSION['_intended'] ?? $default;
        unset($_SESSION['_intended']);
        return self::to($intended, $status);
    }

    public static function refresh(): RedirectResponse
    {
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        return self::to($uri);
    }
}
