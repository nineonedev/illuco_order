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

        if ($this->exists()) {
            if (!$force) {
                throw new RuntimeException("Symbolic link already exists: {$this->link}");
            }

            if (!@unlink($this->link)) {
                throw new RuntimeException("Failed to delete existing symbolic link: {$this->link}");
            }
        }

        if (file_exists($this->link) && !is_link($this->link)) {
            throw new RuntimeException("Cannot create symlink: {$this->link} already exists and is not a symlink.");
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
}