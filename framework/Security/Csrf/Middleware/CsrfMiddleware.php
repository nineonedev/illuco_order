<?php

namespace Framework\Security\Csrf\Middleware;

use Closure;
use Framework\Http\Contracts\MiddlewareInterface;
use Framework\Http\Request;
use Framework\Http\Response;
use Framework\Security\Contracts\TokenManagerInterface;
use RuntimeException;

class CsrfMiddleware implements MiddlewareInterface
{
    protected TokenManagerInterface $tokens; 

    public function __construct(TokenManagerInterface $tokens)
    {
        $this->tokens = $tokens; 
    }

    public function handle(Request $request, Closure $next, ...$args): Response
    {
        if ($this->isReading($request)) {
            return $next($request);
        }

        $token = $request->input('_token') ?? $request->header('X-CSRF-TOKEN'); 

        if (!$token || !$this->tokens->verify($token)) {
            throw new RuntimeException("CSRF token mismatch."); 
        }

        return $next($request); 
    }

    protected function isReading(Request $request): bool
    {
        return in_array($request->method(), ['GET', 'HEAD', 'OPTIONS']);
    }
}