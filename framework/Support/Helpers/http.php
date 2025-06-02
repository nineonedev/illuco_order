<?php

use Framework\Http\Redirector;
use Framework\Http\Request;
use Framework\Http\Response;

if (!function_exists('abort')) {
    function abort(int $statusCode = 404, string $message = 'Not Found')
    {
        $response = new Response($message, $statusCode, [
            'Content-Type' => 'text/plain'
        ]);

        $response->send(); 
        exit;
    }
}

if (!function_exists('response')) {
    function response(string $content = '', int $statusCode = 200, array $headers = [])
    {
        return new Response($content, $statusCode, $headers);
    }
}

if (!function_exists('redirect')) {
    function redirect(string $to = '/'): Response
    {
        return (new Redirector())->to($to);
    }
}

if (!function_exists('request')) {
    function request(): Request
    {
        return app(Request::class);
    }
}