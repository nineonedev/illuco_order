<?php

namespace Framework\Support\Exceptions;

use Exception;
use Framework\Http\Response;
use Framework\Http\Responses\HtmlResponse;
use Framework\Http\Responses\JsonResponse;

abstract class BaseException extends Exception
{
    protected array $meta;

    public function __construct(string $message = '', int $code = 500, array $meta = [])
    {
        parent::__construct($message, $code);
        $this->meta = $meta;
    }

    public function getMeta(): array
    {
        return $this->meta;
    }

    protected function view(): string
    {
        return config('path.error');
    }

    protected function json(): array
    {
        return [];
    }

    public function renderHtmlResponse(): HtmlResponse
    {
        return Response::html(
            render($this->view(), [
                'code'    => $this->getCode(),
                'message' => $this->getMessage(),
                'meta'    => $this->getMeta(),
            ]),
            $this->getCode()
        );
    }

    public function renderJsonResponse(): JsonResponse
    {
        $data = array_merge([
            'success' => false,
            'message' => $this->getMessage(),
            'meta'    => $this->getMeta(),
        ], $this->json());
        return Response::json($data, $this->getCode());
    }
}