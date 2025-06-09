<?php

namespace Framework\Support\Exceptions;

class ValidationException extends RenderableException
{
    protected array $errors = [];

    public function __construct(array $errors, ?string $message = null, int $code = 422)
    {
        parent::__construct($message ?? lang('system.validation.failed'), $code);
        $this->errors = $errors;
    }

    public function errors(): array
    {
        return $this->errors;
    }

    public function renderJson(bool $debug = false): ?\Framework\Http\Response
    {
        return \Framework\Http\ApiResponse::fail(
            $this->getMessage(),
            $this->errors,
            $this->getCode(),
            $debug ? $this->debug() : null
        );
    }

    public function renderHtml(): ?\Framework\Http\Response
    {
        return back()
            ->withErrors($this->errors)
            ->withInput(request()->all());
    }

    public function renderForCli(): void
    {
        echo "[Validation Error] {$this->getMessage()}" . PHP_EOL;

        foreach ($this->errors as $field => $messages) {
            echo "- {$field}: " . implode(', ', (array) $messages) . PHP_EOL;
        }

        if (config('app.debug')) {
            echo $this->getTraceAsString() . PHP_EOL;
        }
    }
}
