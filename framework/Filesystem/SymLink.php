<?php 

namespace Framework\Filesystem;

use RuntimeException;

class SymLink
{
    protected string $link; // 사용자 접근 경로
    protected string $target; // 원본

    public function __construct(string $target, string $link)
    {
        $this->target = $target; 
        $this->link = $link; 
    }

    public function create(bool $force = false): void
    {
        $linkDir = dirname($this->link);

        if (!is_dir($linkDir)) {
            if (!@mkdir($linkDir, 0755, true)) {
                throw new RuntimeException("Failed to create directory for link: {$linkDir}");
            }
        }

        // 이미 존재할 때
        if (file_exists($this->link)) {
            // 이미 심볼릭 링크라면
            if (is_link($this->link)) {
                $currentTarget = readlink($this->link);
                if ($currentTarget === $this->target) {
                    // 이미 원하는 타겟 → 그냥 성공 처리
                    return;
                }
                // force 옵션 아니면 에러
                if (!$force) {
                    throw new RuntimeException("Symbolic link already exists: {$this->link} (points to {$currentTarget})");
                }
                // force면 기존 링크 삭제
                if (!@unlink($this->link)) {
                    throw new RuntimeException("Failed to delete existing symbolic link: {$this->link}");
                }
            } else {
                // 심볼릭 링크가 아니면 무조건 에러
                throw new RuntimeException("Cannot create symlink: {$this->link} already exists and is not a symlink.");
            }
        }

        if (!@symlink($this->target, $this->link)) {
            $error = error_get_last();
            throw new RuntimeException("Failed to create symbolic link: {$this->link} => {$this->target} - {$error['message']}");
        }
    }


    public function exists(): bool
    {
        return is_link($this->link);
    }

    public function delete(): bool
    {
        if (!$this->exists()) {
            return false;
        } 

        return unlink($this->link); 
    }

    public function link(): string
    {
        return $this->link; 
    }
    
    public function target(): string
    {
        if (!$this->exists()) {
            throw new RuntimeException("Link does not exist: {$this->link}"); 
        }

        // readlink는 읽은 링크의 원본 경로를 반환함
        return readlink($this->link); 
    }

    public function isBroken(): bool
    {
        return $this->exists() && !file_exists($this->target()); 
    }

    public function isPointingTo($target): bool
    {
        return $this->exists() && readlink($this->link) === $target;
    }
}