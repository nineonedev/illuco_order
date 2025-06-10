<?php

namespace Framework\Http;

use Framework\Constants\AuthConstants;
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

    protected array $session = [];
    protected array $oldInput = [];
    protected ?string $downloadPath = null;
    protected ?string $downloadName = null;

    protected array $headers = []; 
    protected array $cookies = [];

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

    public function withSession(string $key, $value)
    {
        $this->session[$key] = $value;
        return $this;
    }

    public function withInput(array $input)
    {
        $this->oldInput = $input;
        return $this;
    }

     public function withHeader(string $name, string $value)
    {
        $this->headers[$name] = $value;
        return $this;
    }


    public function withCookie(string $name, string $value, array $options = [])
    {
        $this->cookies[$name] = ['value' => $value, 'options' => $options];
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

    public function download(string $path, string $name = null)
    {
        $this->downloadPath = $path;
        $this->downloadName = $name;
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
        // Apply headers
        foreach ($this->headers as $key => $value) {
            header("{$key}: {$value}");
        }

        // Apply cookies
        foreach ($this->cookies as $name => $cookie) {
            setcookie($name, $cookie['value'], $cookie['options'] ?? []);
        }

        // Apply session flash data
        foreach ($this->session as $key => $value) {
            session()->flash($key, $value);
        }

        // Store old input
        if (!empty($this->oldInput)) {
            session()->flash(AuthConstants::OLD_INPUT, $this->oldInput);
        }

        // File download
        if ($this->downloadPath) {
            return Response::download($this->downloadPath, $this->downloadName);
        }

        // JSON 응답
        if (request()->expectsJson()) {
            return $this->success
                ? ApiResponse::success($this->data, $this->message, $this->status, $this->meta)
                : ApiResponse::fail($this->message, $this->errors, $this->status);
        }

        // 리다이렉트
        if ($this->redirect) {
            return redirect($this->redirect);
        }

        if ($this->redirectRoute) {
            return redirect_route($this->redirectRoute);
        }

        // HTML 뷰 응답
        $view = $this->view ?? $this->success ? config('app.fallbacks.success') : 'app.fallbacks.error';

        if ($this->success) {
            return Response::view($view, $this->data ?? [], $this->status);
        }

        return Response::view($view, [
            'message' => $this->message,
            'errors'  => $this->errors,
        ], $this->status);
    }

}
