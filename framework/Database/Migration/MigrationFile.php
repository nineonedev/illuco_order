<?php

namespace Framework\Database\Migration;

class MigrationFile
{
    /**
     * 주어진 디렉토리에서 모든 마이그레이션 파일을 읽고
     * 정렬된 배열 [파일명 => 클래스명] 반환
     *
     * @return array<string, string>
     */
    public static function all(string $directory): array
    {
        $files = glob(rtrim($directory, '/') . '/*.php');
        sort($files); // 시간순 정렬
        return $files;
    }
    /**
     * 파일명으로부터 클래스명 추출
     *
     * e.g. 20240601_000000_create_users_table → CreateUsersTable
     */
    public static function classFromFile(string $filename): string
    {
        $name = preg_replace('/^\d{8}_\d{6}_/', '', $filename);
        $words = explode('_', $name);

        return implode('', array_map('ucfirst', $words));
    }
}
