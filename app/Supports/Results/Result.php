<?php

namespace App\Supports\Results;

use Framework\Http\Response;
use Framework\Http\Responses\AbstractResponse;

class Result
{
    protected bool $success;
    protected $data;
    protected ?string $message;
    protected array $errors = [];
    protected array $meta = [];
    protected int $status;

    /**
     * Result 생성자
     */
    protected function __construct(
        bool $success,
        $data = null,
        ?string $message = null,
        array $errors = [],
        array $meta = [],
        int $status = 200
    ) {
        $this->success = $success;
        $this->data = $this->normalizeData($data);
        $this->message = $message;
        $this->errors = $errors;
        $this->meta = $meta;
        $this->status = $status;
    }

    /**
     * 데이터 변환 (Resource, Entity, Collection 자동 toArray 변환)
     */
    protected function normalizeData($data)
    {
        if (is_object($data)) {
            // Laravel-style: toArray 지원 객체라면 자동 변환
            if (method_exists($data, 'toArray')) {
                return $data->toArray();
            }
            // JsonSerializable 지원 객체라면 jsonSerialize()로 변환
            if ($data instanceof \JsonSerializable) {
                return $data->jsonSerialize();
            }
        }
        return $data;
    }

    /**
     * 성공 Result 생성
     * @return static
     */
    public static function success($data = null, ?string $message = null, array $meta = [], int $status = 200)
    {
        return new static(true, $data, $message ?? lang('validation.success'), [], $meta, $status);
    }

    /**
     * 실패 Result 생성
     * @return static
     */
    public static function fail(?string $message = null, array $errors = [], $data = null, array $meta = [], int $status = 400)
    {
        return new static(false, $data, $message ?? lang('validation.fail'), $errors, $meta, $status);
    }

    public function isSuccess(): bool
    {
        return $this->success;
    }

    public function getData()
    {
        return $this->data;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function getMeta(): array
    {
        return $this->meta;
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * 배열로 변환
     */
    public function toArray(): array
    {
        return [
            'success' => $this->success,
            'data'    => $this->data,
            'message' => $this->message,
            'errors'  => $this->errors,
            'meta'    => $this->meta,
        ];
    }

    /**
     * HTTP Response 객체로 변환
     */
    public function toResponse(): AbstractResponse
    {
        if ($this->success) {
            return Response::apiSuccess(
                $this->data,
                $this->message ?? lang('validation.success'),
                $this->status,
                $this->meta
            );
        }
        
        return Response::apiFail(
            $this->message ?? lang('validation.fail'),
            $this->errors,
            $this->status,
            $this->meta
        );
    }

    /**
     * JSON 문자열로 반환 (디버깅/로깅용)
     */
    public function toJson(): string
    {
        return json_encode($this->toArray(), JSON_UNESCAPED_UNICODE);
    }
}
