<?php 

namespace Framework\Http\Contracts;

use Closure;
use Framework\Http\Request;
use Framework\Http\Response;

interface MiddlewareInterface 
{
    public function handle(Request $request, Closure $next, ...$args): Response;
}