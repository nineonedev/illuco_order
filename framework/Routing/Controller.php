<?php

namespace Framework\Routing;

use Framework\Core\Application;
use Framework\Http\Response;
use Framework\Http\ApiResponse;
use Framework\Http\Respond;

abstract class Controller
{
    protected Application $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * 통합 응답 처리 (성공/실패 분기)
     */
    public function respond(
        bool $success,
        string $view,
        string $message = '',
        array $data = [],
        array $errors = [],
        int $status = 200,
        ?array $debug = null,
        array $meta = []
    ): Response {
        return $success
            ? $this->render($view, $data, $message, $status, $meta)
            : $this->renderError($view, $message, $errors, $status, $debug);
    }

    /**
     * Respond 빌더 사용
     */
    public function respondWith(): Respond
    {
        return Respond::make();
    }

    /**
     * API or HTML 응답 렌더링
     */
    protected function render(
        string $view,
        array $data = [],
        string $message = 'success',
        int $status = 200,
        array $meta = []
    ): Response {
        if (request()->expectsJson()) {
            return $this->apiSuccess($data, $message, $status, $meta);
        }

        return $this->view($view, $data, $status);
    }

    /**
     * API or HTML 에러 응답 렌더링
     */
    protected function renderError(
        string $view,
        string $message = 'Error',
        array $errors = [],
        int $status = 400,
        ?array $debug = null
    ): Response {
        if (request()->expectsJson()) {
            return $this->apiFail($message, $errors, $status, $debug);
        }

        return $this->view($view, [
            'message' => $message,
            'errors'  => $errors,
        ], $status);
    }

    /**
     * 단순 JSON 응답
     */
    protected function json(array $data, int $status = 200): Response
    {
        return Response::json($data, $status);
    }

    /**
     * API 성공 응답
     */
    protected function apiSuccess(
        $data = null,
        string $message = 'success',
        int $status = 200,
        array $meta = []
    ): Response {
        return ApiResponse::success($data, $message, $status, $meta);
    }

    /**
     * API 실패 응답
     */
    protected function apiFail(
        string $message = 'Error',
        array $errors = [],
        int $status = 400,
        ?array $debug = null
    ): Response {
        return ApiResponse::fail($message, $errors, $status, $debug);
    }

    /**
     * 라우트 리다이렉트
     */
    protected function redirectRoute(string $name, array $params = [], int $status = 302): Response
    {
        $url = route($name, $params);
        return $this->redirect($url, $status);
    }

    /**
     * 일반 리다이렉트
     */
    protected function redirect(string $url, int $status = 302): Response
    {
        return Response::redirect($url, $status);
    }

    /**
     * 백 리다이렉트
     */
    protected function back(): Response
    {
        return Response::back();
    }

    /**
     * View 응답
     */
    protected function view(string $template, array $data = [], int $status = 200): Response
    {
        return Response::view($template, $data, $status);
    }
}
