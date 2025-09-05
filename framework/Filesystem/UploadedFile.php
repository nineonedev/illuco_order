<?php 

namespace Framework\Filesystem;

use RuntimeException;

class UploadedFile 
{
    protected string $path; // 업로드 임시 파일 경로 (tmp)
    protected string $originalName; 
    protected string $mimeType; 
    protected int $size; 
    protected int $error; 
    protected ?string $newName = null; // 저장 후 파일명
    protected ?string $storagePath = null; // 실제 저장 디렉토리
    
    protected ?string $relativePath = null;


    public function __construct(
        string $path,
        string $originalName,
        string $mimeType,
        int $size,
        int $error
    ) {
        $this->path = $path; 
        $this->originalName = $originalName; 
        $this->mimeType = $mimeType; 
        $this->size = $size; 
        $this->error = $error; 
    }

    public static function createFromGlobal(array $file): self
    {
        return new self(
            $file['tmp_name'], 
            $file['name'], 
            $file['type'], 
            $file['size'], 
            $file['error']
        ); 
    }

    public function storeAs(Disk $disk, ?string $subdir = null, ?string $name = null): string
    {
        if ($subdir) {
            $disk = $disk->toWork($subdir); // toWork는 clone 반환으로 가정
        }
        
        return $this->move($disk->path(), $name);
    }

    public function move(string $destinationPath, ?string $newName = null): string
    {
        if ($newName === null) {
            $newName = $this->generateSafeFileName();
        }

        $fullPath = rtrim($destinationPath, DS) . DS . $newName;
        $this->newName = $newName;
        $this->storagePath = $destinationPath;

        if (!is_uploaded_file($this->path)) {
            throw new RuntimeException("The file is not valid uploaded file.");
        }
        if (!move_uploaded_file($this->path, $fullPath)) {
            throw new RuntimeException("Failed to move uploaded file.");
        }

        $root   = uploads_root(); // 절대경로 보장
        $dirRel = ltrim(str_replace(rtrim($root, DS), '', rtrim($this->storagePath, DS)), DS);
        $this->relativePath = trim($dirRel . '/' . $this->newName, '/');

        return $fullPath;
    }

    public function getRelativePath(): string
    {
        if (!$this->relativePath) {
            throw new RuntimeException("File must be moved before accessing relative path.");
        }
        return $this->relativePath; // ex) producttemplate/abcd.jpg
    }


    protected function generateSafeFileName(): string
    {
        $ext = $this->extension();
        $ext = $ext ? ('.' . strtolower($ext)) : '';
        $hash = bin2hex(random_bytes(16)); // 32자리 해시
        return $hash . $ext;
    }

    public function extension(): string
    {
        return pathinfo($this->originalName, PATHINFO_EXTENSION);
    }

    public function toArray(): array
    {
        return [
            'original_name' => $this->originalName,
            'name'          => $this->newName,
            'mime_type'     => $this->mimeType,
            'size'          => $this->size,
            'extension'     => $this->extension(),
            'upload_path'   => $this->getRelativePath(),
        ];
    }

    public function getClientOriginalName(): string
    {
        return $this->originalName;
    }

    public function getClientMimeType(): string
    {
        return $this->mimeType;
    }

    public function getSize(): int
    {
        return $this->size;
    }

    public function getError(): int
    {
        return $this->error;
    }

    public function isValid(): bool
    {
        return $this->error === UPLOAD_ERR_OK && is_uploaded_file($this->path);
    }

    public function getStoragePath(): ?string
    {
        return $this->storagePath;
    }

    public function getName(): ?string
    {
        return $this->newName; 
    }

    public function getUploadPath(): string
    {
        if (!$this->storagePath || !$this->newName) {
            throw new RuntimeException("File must be moved before accessing upload path.");
        }
        
        return upload_path($this->getStoragePath() . DS . $this->getName());
    }

    public function getUploadUrl(): string
    {
        return uploads_url($this->getRelativePath());
    }
}
