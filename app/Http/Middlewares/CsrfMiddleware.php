<?php

namespace App\Http\Middlewares;

use Closure;
use Framework\Http\Request;
use Framework\Http\Contracts\MiddlewareInterface;
use Framework\Http\Contracts\ResponseInterface;
use Framework\Security\Csrf\TokenManager;
use Framework\Security\Csrf\TokenManagerInterface;
use Framework\Support\Exceptions\Http\ForbiddenException;

class CsrfMiddleware implements MiddlewareInterface
{
    protected TokenManagerInterface $tokens;

    public function __construct(TokenManagerInterface $tokens)
    {
        $this->tokens = $tokens;
    }

    public function handle(Request $request, Closure $next, ...$args): ResponseInterface
    {
        if ($this->isReading($request) || $this->shouldSkip($request)) {
            return $next($request);
        }

        $token = $request->input(TokenManager::CSRF_INPUT_NAME)
                ?? $request->header('X-CSRF-TOKEN');

        if (!$token || !$this->tokens->verify($token)) {
            throw new ForbiddenException(lang('validation.csrf_mismatch'));
        }

        return $next($request);
    }

    protected function shouldSkip(Request $request): bool
    {
        return $request->is('auth/login') && $request->method() === 'POST'; 
    }

    protected function isReading(Request $request): bool
    {
        return in_array($request->method(), ['GET', 'HEAD', 'OPTIONS']);
    }
}
