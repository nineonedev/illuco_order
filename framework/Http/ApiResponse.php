<?php

namespace Framework\Http;

class ApiResponse extends Response
{
    /**
     * 성공 응답 (기본 메시지: 'success')
     */
    public static function success(
        $data = null,
        string $message = 'success',
        int $statusCode = 200,
        array $meta = []
    ): self {
        $payload = [
            'success' => true,
            'data'    => $data,
            'message' => $message,
        ];
        if (!empty($meta)) {
            $payload['meta'] = $meta;
        }

        return new static(
            json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT),
            $statusCode,
            ['Content-Type' => 'application/json']
        );
    }

    /**
     * 실패/에러 응답 (기본 메시지: 'Error')
     */
    public static function fail(
        string $message = 'Error',
        ?array $errors = null,
        int $statusCode = 400,
        ?array $debug = null
    ): self {
        $payload = [
            'success' => false,
            'message' => $message,
            'code'    => $statusCode,
        ];
        if (!empty($errors)) {
            $payload['errors'] = $errors;
        }
        if (!empty($debug)) {
            $payload['debug'] = $debug;
        }

        return new static(
            json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT),
            $statusCode,
            ['Content-Type' => 'application/json']
        );
    }


    /**
     * 빌더 패턴
     * @return static
     */
    public static function builder()
    {
        return new static('', 200, ['Content-Type' => 'application/json']);
    }

    /**
     * data 필드 추가/수정
     * @return static
     */
    public function withData($data)
    {
        $payload = json_decode($this->content, true) ?: [];
        $payload['data'] = $data;
        $this->content = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        return $this;
    }

    /**
     * message 필드 추가/수정
     * @return static
     */
    public function withMessage(string $msg)
    {
        $payload = json_decode($this->content, true) ?: [];
        $payload['message'] = $msg;
        $this->content = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        return $this;
    }

    /**
     * meta 필드 추가/수정
     * @return static
     */
    public function withMeta(array $meta)
    {
        $payload = json_decode($this->content, true) ?: [];
        $payload['meta'] = $meta;
        $this->content = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        return $this;
    }

    /**
     * errors 필드 추가/수정
     * @return static
     */
    public function withErrors(array $errors)
    {
        $payload = json_decode($this->content, true) ?: [];
        $payload['errors'] = $errors;
        $this->content = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        return $this;
    }
}
