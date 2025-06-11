<?php

namespace Framework\Security\Csrf\Middleware;

use Closure;
use Framework\Constants\AuthConstants;
use Framework\Http\Contracts\MiddlewareInterface;
use Framework\Http\Contracts\ResponseInterface;
use Framework\Http\Request;
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
        if ($this->isReading($request)) {
            return $next($request);
        }

        $token = $request->input(AuthConstants::CSRF_TOKEN_KEY)
                ?? $request->header('X-CSRF-TOKEN');

        if (!$token || !$this->tokens->verify($token)) {
            throw new ForbiddenException('유효하지 않은 CSRF 토큰입니다.');
        }

        return $next($request);
    }

    protected function isReading(Request $request): bool
    {
        return in_array($request->method(), ['GET', 'HEAD', 'OPTIONS']);
    }
}
