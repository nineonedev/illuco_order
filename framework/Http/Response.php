<?php

namespace Framework\Http;

use Framework\Http\Responses\DownloadResponse;
use Framework\Http\Responses\HtmlResponse;
use Framework\Http\Responses\JsonResponse;
use Framework\Http\Responses\RedirectResponse;
use Framework\Http\Responses\ViewResponse;

class Response
{

    public static function configure(): ResponseBuilder
    {
        return new ResponseBuilder();
    }

    /**
     * HTML 응답
     */
    public static function html(string $content = '', int $statusCode = 200, array $headers = []): HtmlResponse
    {
        return new HtmlResponse($content, $statusCode, $headers);
    }

    /**
     * JSON 응답
     */
    public static function json($data = [], int $statusCode = 200, array $headers = []): JsonResponse
    {
        return new JsonResponse($data, $statusCode, $headers);
    }

    /**
     * 뷰(템플릿) 응답
     */
    public static function view(string $template, array $data = [], int $statusCode = 200, array $headers = []): ViewResponse
    {
        return new ViewResponse($template, $data, $statusCode, $headers);
    }

    
    /**
     * 이전 페이지로 리다이렉트
     */
    public static function back(): RedirectResponse
    {
        $url = session()->get('_previous_url', '/');
        return new RedirectResponse($url);
    }

    /**
     * 리다이렉트 응답
     */
    public static function redirect(string $to, int $statusCode = 302, array $headers = []): RedirectResponse
    {
        return new RedirectResponse($to, $statusCode, $headers);
    }

    /**
     * 네임드 라우트 기반 리다이렉트 응답
     *
     */
    public static function redirectRoute(string $routeName, array $params = [], int $statusCode = 302, array $headers = []): RedirectResponse
    {
        $url = route($routeName, $params);
        return new RedirectResponse($url, $statusCode, $headers);
    }

    /**
     * 파일 다운로드 (단일/스트림 선택)
     */
    public static function download(
        string $filePath,
        ?string $downloadName = null,
        bool $stream = false,
        int $chunkSize = 8192,
        int $statusCode = 200,
        array $headers = []
    ): DownloadResponse {
        $response = new DownloadResponse($filePath, $downloadName, $stream, $chunkSize, $statusCode, $headers);
        if ($stream) {
            $response->asStream($chunkSize);
        }
        return $response;
    }


    /**
     * API 성공 응답 (JsonResponse로 통일)
     */
    public static function apiSuccess(
        $data = null,
        string $message = 'success',
        int $status = 200,
        array $meta = []
    ): JsonResponse {
        $payload = [
            'success' => true,
            'data'    => $data,
            'message' => $message,
        ];
        if (!empty($meta)) {
            $payload['meta'] = $meta;
        }
        return new JsonResponse($payload, $status);
    }

    /**
     * API 실패 응답 (JsonResponse로 통일)
     */
    public static function apiFail(
        string $message = 'Error',
        array $errors = [],
        int $status = 400,
        ?array $debug = null
    ): JsonResponse {
        $payload = [
            'success' => false,
            'message' => $message,
            'errors'  => $errors,
        ];
        if (!empty($debug)) {
            $payload['debug'] = $debug;
        }
        return new JsonResponse($payload, $status);
    }
}
