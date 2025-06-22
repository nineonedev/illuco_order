<?php

namespace Framework\Routing;

use Closure;
use Framework\Core\Application;
use Framework\Http\Contracts\ResponseInterface;
use Framework\Http\Response;
use Framework\Http\ResponseBuilder;
use Framework\Http\Responses\JsonResponse;
use Framework\Http\Responses\RedirectResponse;
use Framework\Http\Responses\ViewResponse;

abstract class Controller
{
    protected Application $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * ResponseBuilder 사용 (체이닝 방식)
     */
    public function responseWith(): ResponseBuilder
    {
        return ResponseBuilder::create();
    }

    protected function runInTransaction(
        Closure $callback
    ): ResponseInterface {
         try {
            return transaction()->run($callback);
        } catch (\Throwable $e) {
            return $this->renderError(null, '처리 중 오류 발생: ' . $e->getMessage());
        }
    }

    /**
     * 통합 응답 처리 (성공/실패 분기)
     */
    public function response(
        bool $success,
        ?string $view = null,
        string $message = '',
        array $payload = [],
        int $status = 200,
        array $meta = []
    ) {
        $view = $view ?? ($success ? 'supports.success' : 'supports.error');

        return $this->renderHybrid(
            $success,
            $payload,
            $message,
            $status,
            $view,
            $meta
        );
    }

    protected function render(
        ?string $view = null,
        array $data = [],
        string $message = 'success',
        int $status = 200,
        array $meta = []
    ) {
        $view = $view ?: 'supports.success';
        return $this->renderHybrid(true, $data, $message, $status, $view, $meta);
    }

    protected function renderBack(
        string $message = '처리가 완료되었습니다.',
        array $params = [],
        int $status = 302
    ): RedirectResponse {
        return Response::back()
            ->with('success', $message)
            ->withMany($params);
    }

    protected function renderError(
        ?string $view = null,
        string $message = 'Error',
        array $errors = [],
        int $status = 400
    ) {
        $view = $view ?: 'supports.error';
        return $this->renderHybrid(false, $errors, $message, $status, $view);
    }


    /**
     * 공통 응답 처리 (JSON or HTML)
     * @return JsonResponse|ViewResponse
     */
    protected function renderHybrid(
        bool $success = true,
        array $payload = [],
        string $message = '',
        int $status = 200,
        string $view = '',
        array $meta = [],
        array $extraViewData = []
    ) {
        if (request()->expectsJson()) {
            return $success
                ? Response::apiSuccess($payload, $message, $status, $meta)
                : Response::apiFail($message, $payload, $status);
        }

        return $this->view($view, array_merge(['message' => $message], $payload, $extraViewData), $status);
    }


    /**
     * 단순 JSON 응답
     * @return JsonResponse
     */
    protected function json($data = [], int $status = 200)
    {
        return Response::json($data, $status);
    }

    /**
     * 리다이렉트 to 라우트 이름
     * @return RedirectResponse
     */
    protected function redirectRoute(string $name, array $params = [], int $status = 302)
    {
        $url = route($name, $params);
        return $this->redirect($url, $status);
    }

    /**
     * 일반 리다이렉트
     * @return RedirectResponse
     */
    protected function redirect(string $url, int $status = 302)
    {
        return Response::redirect($url, $status);
    }

    /**
     * 백 리다이렉트
     * @return RedirectResponse
     */
    protected function back()
    {
        return Response::back();
    }

    /**
     * View 응답
     * @return ViewResponse
     */
    protected function view(string $template, array $data = [], int $status = 200)
    {
        return Response::view($template, $data, $status);
    }
}
