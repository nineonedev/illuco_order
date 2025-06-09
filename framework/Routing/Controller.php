<?php

namespace Framework\Routing;

use Framework\Core\Application;
use Framework\Http\Request;
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
     * API or HTML 응답 렌더링
     */
    protected function render(
        Request $request,
        string $view,
        array $data = [],
        string $message = 'success',
        int $status = 200,
        array $meta = []
    ): Response {
        if ($request->expectsJson()) {
            return $this->apiSuccess($data, $message, $status, $meta);
        }

        return $this->view($view, $data, $status);
    }

    /**
     * API or HTML 에러 응답 렌더링
     */
    protected function renderError(
        Request $request,
        string $view,
        string $message = 'Error',
        array $errors = [],
        int $status = 400,
        ?array $debug = null
    ): Response {
        if ($request->expectsJson()) {
            return $this->apiFail($message, $errors, $status, $debug);
        }

        return $this->view($view, [
            'message' => $message,
            'errors'  => $errors,
        ], $status);
    }

    /**
     * 단순 JSON 응답 (legacy용)
     */
    protected function json(array $data, int $status = 200): Response
    {
        return Response::json($data, $status);
    }

    /**
     * 구조화된 API 성공 응답
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
     * 구조화된 API 실패 응답
     */
    protected function apiFail(
        string $message = 'Error',
        array $errors = [],
        int $status = 400,
        ?array $debug = null
    ): Response {
        return ApiResponse::fail($message, $errors, $status, $debug);
    }

    protected function redirectRoute(string $name, array $params = [], int $status = 302): Response
    {
        $url = route($name, $params);
        return $this->redirect($url, $status);
    }
    
    /**
     * 리다이렉트 응답
     */
    protected function redirect(string $url, int $status = 302): Response
    {
        return Response::redirect($url, $status);
    }

    /**
     * 이전 페이지로 리다이렉트
     */
    protected function back(): Response
    {
        return Response::back();
    }

    /**
     * HTML View 응답
     */
    protected function view(string $template, array $data = [], int $status = 200): Response
    {
        return Response::view($template, $data, $status);
    }
}
