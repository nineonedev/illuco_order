<?php

namespace Framework\Http;

use Framework\Http\Response;
use Framework\Http\ApiResponse;

class Respond
{
    protected bool $success = true;
    protected string $message = 'success';
    protected int $status = 200;
    protected array $errors = [];
    protected $data = null;
    protected array $meta = [];
    protected ?string $view = null;
    protected ?string $redirect = null;
    protected ?string $redirectRoute = null;

    /**
     * @return static
     */
    public static function make()
    {
        return new static();
    }

    /**
     * @return static
     */
    public function configure(array $options)
    {
        foreach ($options as $key => $value) {
            if (method_exists($this, $key)) {
                $this->{$key}($value);
            }
        }
        return $this;
    }

    /**
     * @return static
     */
    public function success(bool $flag = true)
    {
        $this->success = $flag;
        return $this;
    }

    /**
     * @return static
     */
    public function message(string $successMessage, ?string $failMessage = null)
    {
        $this->message = $this->success ? $successMessage : ($failMessage ?? $successMessage);
        return $this;
    }

    /**
     * @return static
     */
    public function status(int $statusSuccess, ?int $statusFail = null)
    {
        $this->status = $this->success ? $statusSuccess : ($statusFail ?? $statusSuccess);
        return $this;
    }

    /**
     * @return static
     */
    public function data($successData, $failData = null)
    {
        $this->data = $this->success ? $successData : ($failData ?? []);
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
    public function meta(array $meta)
    {
        $this->meta = $meta;
        return $this;
    }

    /**
     * @return static
     */
    public function view(string $template)
    {
        $this->view = $template;
        return $this;
    }

    /**
     * @return static
     */
    public function whenSuccess(callable $callback)
    {
        if ($this->success) {
            $callback($this);
        }
        return $this;
    }

    /**
     * @return static
     */
    public function whenFail(callable $callback)
    {
        if (!$this->success) {
            $callback($this);
        }
        return $this;
    }

    /**
     * @return static
     */
    public function redirect(string $url)
    {
        $this->redirect = $url;
        return $this;
    }

    /**
     * @return static
     */
    public function redirectRoute(string $name)
    {
        $this->redirectRoute = $name;
        return $this;
    }

    /**
     * 최종 응답 전송
     */
    public function send(): Response
    {
        if (request()->expectsJson()) {
            return $this->success
                ? ApiResponse::success($this->data, $this->message, $this->status, $this->meta)
                : ApiResponse::fail($this->message, $this->errors, $this->status);
        }

        if ($this->redirect) {
            return redirect($this->redirect);
        }

        if ($this->redirectRoute) {
            return redirect_route($this->redirectRoute);
        }

        if ($this->success) {
            return Response::view($this->view ?? '', $this->data ?? [], $this->status);
        }

        return Response::view($this->view ?? '', [
            'message' => $this->message,
            'errors'  => $this->errors,
        ], $this->status);
    }
}
