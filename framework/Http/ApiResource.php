<?php

namespace Framework\Http;

use Framework\Database\ORM\Entities\Entity;

/**
 * API Resource의 베이스 클래스 (Laravel Resource 스타일)
 */
abstract class ApiResource
{
    protected Entity $entity;

    /** @var array 동적으로 포함할 추가 데이터 */
    protected array $with = [];

    public function __construct($entity)
    {
        $this->entity = $entity;
    }

    /**
     * API 응답으로 내보낼 데이터 구조 정의 (자식에서 구현)
     */
    abstract public function toArray(): array;

    /**
     * 동적으로 추가 필드/관계를 포함 (chaining)
     * ex) ->with(['token' => 'xxx'])
     * @return static
     */
    public function with(array $data)
    {
        $this->with = array_merge($this->with, $data);
        return $this;
    }

    /**
     * 최종 응답 데이터 배열 반환 (추가 필드 포함)
     */
    public function resolve(): array
    {
        return array_merge($this->toArray(), $this->with);
    }

    /**
     * 바로 JSON으로 변환
     */
    public function toJson(int $flags = 0): string
    {
        return json_encode($this->resolve(), JSON_UNESCAPED_UNICODE | $flags);
    }

    /**
     * 여러 엔티티 배열을 한 번에 변환 (ex: UserResource::collection($users))
     */
    public static function collection(iterable $entities): array
    {
        $self = static::class;
        return array_map(fn($e) => (new $self($e))->resolve(), $entities);
    }
}
