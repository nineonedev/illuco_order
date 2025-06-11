<?php 

namespace Framework\Http\Contracts;

use Closure;
use Framework\Http\Request;

interface MiddlewareInterface 
{
    public function handle(Request $request, Closure $next, ...$args): ResponseInterface;
}