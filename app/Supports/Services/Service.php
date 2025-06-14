<?php

namespace App\Supports\Services;

use App\Supports\Results\Result;

abstract class Service 
{
    /**
     * 메인 실행 로직
     * @return mixed
     */
    abstract protected function handle(array $payload): array;

    /**
     * 일반 실행
     */
    public function run(array $payload): Result
    {
        try {
            $payload = $this->prepare($payload);
            $this->validate($payload);
            $result = $this->handle($payload);
            return Result::success($result);

        } catch (\Throwable $e) {
            return Result::fail($e->getMessage());
        }
    }

    /**
     * 트랜잭션 내에서 실행
     */
    public function runInTransaction(array $payload): Result
    {
        try {
            $payload = $this->prepare($payload);
            $this->validate($payload);

            $result = transaction()->run(function () use ($payload) {
                return $this->handle($payload);
            });

            return Result::success($result);

        } catch (\Throwable $e) {

            return Result::fail($e->getMessage());
        }
    }

    /**
     * 실행 전 전처리 로직 (선택)
     */
    protected function prepare(array $payload): array
    {
        return $payload;
    }

    /**
     * 실행 전 유효성 검사 (선택)
     */
    protected function validate(array $payload): void
    {
        // 필요 시 자식 클래스에서 override
    }
}