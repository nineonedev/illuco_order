<?php 

namespace App\Domains\System\Entities;

use App\Domains\System\Repositories\FileAttachmentRepository;
use Framework\Database\ORM\Entities\MorphEntity;


class FileAttachment extends MorphEntity
{
    protected array $fillable = [
        'original_name', 
        'name',
        'extension',
        'mime_type',
        'size', 
        'extension',
        'path',
        'sort_order',
        'file_key',
    ];

    protected array $casts = [
        'size' => 'int',
        'sort_order' => 'int',
    ];


    public static function morphType(): string
    {
        return 'file_attachable';
    }

    public static function repositoryClass(): string
    {
        return FileAttachmentRepository::class;
    }

    public function storagePath(): string
    {
        return rtrim($this->path, DS) . DS . $this->name;
    }

    public function uploadPath(): string
    {
        return upload_path($this->path . DS . $this->name);
    }
}