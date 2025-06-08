<?php

use Framework\Http\ApiResponse;
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

if (!function_exists('back')) {
    function back(): Response
    {
        return Response::back();
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

if (!function_exists('api_ok')) {
    function api_ok(
        $data = null, 
        string $message = 'success', 
        array $meta = [], 
        int $status = 200
    ): Response 
    {
        return ApiResponse::ok($data, $message, $meta, $status);
    }
}

if (!function_exists('api_error')) {
    function api_error(
        string $message = 'Error', 
        int $status = 400, 
        array $errors = [], 
        ?array $debug = null
    ): Response 
    {
        return ApiResponse::error($message, $status, $errors, $debug);
    }
}

