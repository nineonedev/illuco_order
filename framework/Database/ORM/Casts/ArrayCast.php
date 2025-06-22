<?php

namespace Framework\Database\ORM\Casts;

class ArrayCast implements CastInterface
{
    /**
     * Convert array to JSON string for DB storage
     *
     * @param mixed $value
     * @return string|null
     */
    public function set($value)
    {
        if (is_null($value)) {
            return null;
        }

        // 배열을 JSON 문자열로 변환 (옵션 없이 기본 변환)
        return json_encode($value);
    }

    /**
     * Convert JSON string from DB to PHP array
     *
     * @param mixed $value
     * @return array|null
     */
    public function get($value)
    {
        if (is_null($value)) {
            return [];
        }

        // 이미 배열이면 그대로 반환
        if (is_array($value)) {
            return $value;
        }

        // 값이 문자열이면 JSON을 배열로 변환
        if (is_string($value)) {
            return json_decode($value, true);
        }

        return []; // JSON 파싱 실패 시 빈 배열 반환
    }
}
