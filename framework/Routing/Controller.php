<?php
namespace Framework\Routing;

use Framework\Core\Application;
use Framework\Http\Response;
use Framework\Http\ApiResponse;

abstract class Controller
{
    protected Application $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * 기존 json: 단순 데이터만 반환 (legacy/단순 케이스)
     */
    protected function json(array $data, int $status = 200): Response
    {
        return Response::json($data, $status);
    }

    /**
     * API 표준 성공 응답 (ApiResponse 이용)
     * @param mixed $data
     * @param string $message
     * @param int $status
     * @param array $meta
     */
    protected function apiSuccess($data = null, string $message = 'success', int $status = 200, array $meta = []): Response
    {
        return ApiResponse::success($data, $message, $status, $meta);
    }

    /**
     * API 표준 에러 응답 (ApiResponse 이용)
     * @param string $message
     * @param array $errors
     * @param int $status
     * @param array|null $debug
     */
    protected function apiFail(string $message = 'Error', array $errors = [], int $status = 400, ?array $debug = null): Response
    {
        return ApiResponse::fail($message, $errors, $status, $debug);
    }

    protected function redirect(string $url, int $status = 302): Response
    {
        return Response::redirect($url, $status);
    }

    protected function back(): Response
    {
        return Response::back();
    }

    protected function view(string $template, array $data = [], int $status = 200): Response
    {
        return Response::view($template, $data, $status);
    }
}
