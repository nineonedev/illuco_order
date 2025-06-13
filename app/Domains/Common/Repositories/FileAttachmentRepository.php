<?php

namespace App\Domains\Common\Repositories;

use App\Domains\Common\Entities\FileAttachment;
use Framework\Database\ORM\Entities\Entity;
use Framework\Database\ORM\Repositories\Repository;
use Framework\Filesystem\UploadedFile;

class FileAttachmentRepository extends Repository
{
    public function table(): string
    {
        return 'file_attachments';
    }

    public function entityClass(): string
    {
        return FileAttachment::class;
    }

    public static function uploadOne(Entity $entity, string $filename, ?string $ruleNames = null): ?FileAttachment
    {
        if (!request()->hasFile($filename)) return null; 
        
        if ($ruleNames) {
            request()->validateOrFail([$filename => $ruleNames]);
        }

        $file = request()->file($filename);
        $uploadedFile = UploadedFile::createFromGlobal($file);

        $entityClass = get_class($entity); 

        $directory = $entityClass::alias() . '/' . $entity->getPrimaryKey();
        $disk = disk()->toWork($directory);
        $uploadedFile->storeAs($disk);
        
        $type = static::morphType();
        
        $file = array_merge(
            $uploadedFile->toArray(), 
            [
                "{$type}_type" => $entityClass::morphType(),
                "{$type}_id"   => $entity->getPrimaryKey(),
            ]
        );

        return static::make()->save(FileAttachment::make($file));
    }
}