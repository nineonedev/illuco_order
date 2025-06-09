<?php

namespace Framework\Support\Exceptions;

use Exception;
use Framework\Http\ApiResponse;
use Framework\Http\Response;

abstract class RenderableException extends Exception implements RendersExceptionInterface
{
    protected array $meta = [];

    public function __construct(string $message = '', int $code = 500, array $meta = [])
    {
        parent::__construct($message, $code);
        $this->meta = $meta;
    }

    protected function debug(): ?array
    {
        if (!config('app.debug')) {
            return null;
        }

        return [
            'exception' => get_class($this),
            'file'      => $this->getFile(),
            'line'      => $this->getLine(),
            'trace'     => explode("\n", $this->getTraceAsString()),
            'meta'      => $this->meta,
        ];
    }

    protected function htmlView(): string
    {
        $view = "errors.{$this->getCode()}";
        return view()->exists($view) ? $view : 'errors.default';
    }

    public function renderHtml(): ?Response
    {
        return response()
            ->setStatusCode($this->getCode())
            ->view($this->htmlView(), [
                'message' => $this->getMessage(),
                'debug'   => $this->debug(),
            ]);
    }

    public function renderJson(bool $debug = false): ?Response
    {
        return ApiResponse::fail(
            $this->getMessage(),
            [],
            $this->getCode(),
            $debug ? $this->debug() : null
        );
    }

    public function renderForCli(): void
    {
        echo "[Exception] {$this->getMessage()}" . PHP_EOL;

        if (config('app.debug')) {
            echo "File: {$this->getFile()} @ Line {$this->getLine()}" . PHP_EOL;
            echo $this->getTraceAsString() . PHP_EOL;
        }
    }
}
