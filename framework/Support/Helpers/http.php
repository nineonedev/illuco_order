<?php

use Framework\Http\Request;
use Framework\Http\Response;
use Framework\Http\Responses\HtmlResponse;
use Framework\Http\Responses\JsonResponse;
use Framework\Http\Responses\RedirectResponse;
use Framework\Http\Responses\DownloadResponse;
use Framework\Http\Responses\ViewResponse;

// 즉시 HTML 오류 응답 후 종료
if (!function_exists('abort')) {
    /**
     * @param int $statusCode
     * @param string $message
     * @return never
     */
    function abort(int $statusCode = 404, string $message = 'Not Found'): void
    {
        Response::html(
            "<!doctype html><html lang=\"ko\"><head><meta charset=\"utf-8\"><title>Error $statusCode</title></head><body><h1>$message</h1></body></html>",
            $statusCode
        )->send();
        exit;
    }
}

// 이전 페이지(Referer)로 리다이렉트
if (!function_exists('back')) {
    /**
     * @return RedirectResponse
     */
    function back(): RedirectResponse
    {
        $referer = $_SERVER['HTTP_REFERER'] ?? '/';
        return Response::redirect($referer);
    }
}

// 문자열이면 HTML, 배열/object면 JSON 응답 객체 반환
if (!function_exists('response')) {
    /**
     * @param mixed $content
     * @param int $statusCode
     * @param array $headers
     * @return HtmlResponse|JsonResponse
     */
    function response($content = '', int $statusCode = 200, array $headers = [])
    {
        if (is_array($content) || is_object($content)) {
            return Response::json($content, $statusCode, $headers);
        }
        return Response::html((string)$content, $statusCode, $headers);
    }
}

// 지정 URL로 리다이렉트
if (!function_exists('redirect')) {
    /**
     * @param string $to
     * @param int $statusCode
     * @param array $headers
     * @return RedirectResponse
     */
    function redirect(string $to = '/', int $statusCode = 302, array $headers = []): RedirectResponse
    {
        return Response::redirect($to, $statusCode, $headers);
    }
}

// 파일 다운로드 (단일/스트림)
if (!function_exists('download')) {
    /**
     * @param string $filePath
     * @param string|null $name
     * @param bool $stream
     * @param int $chunkSize
     * @param int $statusCode
     * @param array $headers
     * @return DownloadResponse
     */
    function download(
        string $filePath,
        ?string $name = null,
        bool $stream = false,
        int $chunkSize = 8192,
        int $statusCode = 200,
        array $headers = []
    ): DownloadResponse {
        return Response::download($filePath, $name, $stream, $chunkSize, $statusCode, $headers);
    }
}

// 현재 Request 인스턴스 반환
if (!function_exists('request')) {
    /**
     * @return Request
     */
    function request(): Request
    {
        return app(Request::class);
    }
}

// 표준 API 성공 JSON 응답
if (!function_exists('api_success')) {
    /**
     * @param mixed $data
     * @param string $message
     * @param int $status
     * @param array $meta
     * @return JsonResponse
     */
    function api_success(
        $data = null,
        string $message = 'success',
        int $status = 200,
        array $meta = []
    ): JsonResponse {
        return Response::apiSuccess($data, $message, $status, $meta);
    }
}

// 표준 API 실패/에러 JSON 응답
if (!function_exists('api_fail')) {
    /**
     * @param string $message
     * @param array $errors
     * @param int $status
     * @param array|null $debug
     * @return JsonResponse
     */
    function api_fail(
        string $message = 'Error',
        array $errors = [],
        int $status = 400,
        ?array $debug = null
    ): JsonResponse {
        return Response::apiFail($message, $errors, $status, $debug);
    }
}

// 뷰 응답 (템플릿 렌더링)
if (!function_exists('view')) {
    /**
     * @param string $template
     * @param array $data
     * @param int $status
     * @param array $headers
     * @return ViewResponse
     */
    function view(
        string $template,
        array $data = [],
        int $status = 200,
        array $headers = []
    ): ViewResponse {
        return Response::view($template, $data, $status, $headers);
    }
}
