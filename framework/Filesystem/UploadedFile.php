<?php 

namespace Framework\Filesystem;

use RuntimeException;

class UploadedFile 
{
    protected string $path; 
    protected string $originalName; 
    protected string $mimeType; 
    protected int $size; 
    protected int $error; 
    protected ?string $newName = null; 

    public function __construct(
        string $path,
        string $originalName,
        string $mimeType,
        int $size,
        int $error
    )
    {
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

    public function storeAs(Disk $disk, string $path, ?string $name = null): string
    {
        $targetDir = rtrim($disk->path($path), DS);
        return $this->move($targetDir, $name);
    }

    public function move(string $destinationPath, ?string $newName = null): string
    {
        $newName = $newName ?? $this->originalName; 
        $this->newName = rtrim($destinationPath, DS) . DS . $newName; 

        if (!is_uploaded_file($this->newName)) {
            throw new RuntimeException("The file is not valid uploaded file."); 
        }

        if (!move_uploaded_file($this->path, $this->newName)) {
            throw new RuntimeException("Failed to move uploaded file."); 
        }

        return $this->newName; 
    }

    public function extension(): string
    {
        return pathinfo($this->originalName, PATHINFO_EXTENSION);
    }
    
    public function toArray(): array
    {
        return [
            'original_name' => $this->originalName,
            'new_name'      => $this->newName,
            'mime_type'     => $this->mimeType,
            'size'          => $this->size,
            'error'         => $this->error,
            'path'          => $this->path,
            'extension'     => $this->extension(),
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
}