<?php

namespace Framework\Http;

use Framework\Http\Contracts\ResponseInterface;
use Framework\Http\Responses\JsonResponse;
use Framework\Http\Responses\RedirectResponse;
use Framework\Http\Responses\ViewResponse;

class ResponseBuilder
{
    protected bool $success = true;
    protected string $message = '';
    protected int $status = 200;
    protected $data = null;
    protected array $meta = [];
    protected array $errors = [];
    protected ?string $view = null;

    protected array $flash = [];

    protected ?string $redirectTo = null;
    
    protected ?array $redirectRoute = null;

    /**
     * @return static
     */
    public static function create()
    {
        return new static();
    }

    /**
     * @return static
     */
    public function jsonOnly()
    {
        $this->forceJson = true;
        return $this;
    }

    /**
     * @return static
     */
    public function success(bool $value = true)
    {
        $this->success = $value;
        return $this;
    }

    /**
     * @return static
     */
    public function with(array $flash)
    {
        $this->flash = $flash;
        return $this;
    }

    /**
     * @return static
     */
    public function message(string $success, ?string $fail = null)
    {
        $this->message = $this->success ? $success : ($fail ?? $success);
        return $this;
    }

    /**
     * @return static
     */
    public function data($data)
    {
        $this->data = $data;
        return $this;
    }

    /**
     * @return static
     */
    public function meta(array $meta)
    {
        $this->meta = $meta;
        return $this;
    }

    /**
     * @return static
     */
    public function errors(array $errors)
    {
        $this->errors = $errors;
        return $this;
    }

    /**
     * @return static
     */
    public function status(int $success, ?int $fail = null)
    {
        $this->status = $this->success ? $success : ($fail ?? $success);
        return $this;
    }

    /**
     * @return static
     */
    public function view(string $view)
    {
        $this->view = $view;
        return $this;
    }

    /**
     * @return static
     */
    public function redirect(string $url)
    {
        $this->redirectTo = $url;
        return $this;
    }

    /**
     * @return static
     */
    public function redirectRoute(string $routeName, array $params = [])
    {
        $this->redirectRoute = [$routeName, $params];
        return $this;
    }

    protected function responseJson(): JsonResponse
    {
        return $this->success
            ? Response::apiSuccess($this->data, $this->message, $this->status, $this->meta)
            : Response::apiFail($this->message, $this->errors, $this->status);
    }

    protected function hasRedirect(): bool
    {
        return $this->redirectRoute !== null || $this->redirectTo !== null;
    }

    protected function responseRedirect(): ?RedirectResponse
    {
        if ($this->redirectRoute) {
            [$name, $params] = $this->redirectRoute;
            return Response::redirectRoute($name, $params, $this->status);
        }

        if ($this->redirectTo) {
            return Response::redirect($this->redirectTo, $this->status);
        }

        return null;
    }

    public function responseView(): ViewResponse
    {
        $view = $this->view ?? ($this->success ? 'supports.success' : 'supports.error');

        $payload = $this->success
            ? ['message' => $this->message] + (is_array($this->data) ? $this->data : ['data' => $this->data])
            : ['message' => $this->message, 'errors' => $this->errors];

        return Response::view($view, $payload, $this->status);
    }

    /**
     * 자동 API/웹 응답 객체 생성 및 반환
     * @return ResponseInterface
     */
    public function send()
    {
        if (!empty($this->flash)) {
            flash($this->flash);
        }

        if (request()->expectsJson()) {
            return $this->responseJson();
        }

        if ($this->hasRedirect()) {
            return $this->responseRedirect();
        }

        return $this->responseView();
    }
}
