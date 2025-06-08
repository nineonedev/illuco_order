<?php

namespace App\Domains\User\Repositories;

use App\Domains\User\Entities\File;
use App\Domains\User\Entities\Post;
use Framework\Database\ORM\Entities\Entity;
use Framework\Database\ORM\Entities\HasWorkDirectory;
use Framework\Database\ORM\Entities\Morphable;
use Framework\Database\ORM\Repositories\Repository;
use Framework\Filesystem\UploadedFile;

class FileRepository extends Repository
{
    public function table(): string
    {
        return 'files';
    }

    public function entityClass(): string
    {
        return File::class;
    }

    public static function uploadOne(Entity $entity, string $filename, ?string $ruleNames = null): ?File
    {
        if (!request()->hasFile($filename)) return null; 

        if (
            !is_subclass_of($entity, HasWorkDirectory::class) || 
            !is_subclass_of($entity, Morphable::class)
        ) {
            return null;
        }
        
        if ($ruleNames) {
            request()->validateOrFail([$filename => $ruleNames]);
        }

        $file = request()->file($filename);
        $uploadedFile = UploadedFile::createFromGlobal($file);

        $entityClass = get_class($entity); 

        $directory = $entityClass::workDirectory() . '/' . $entity->getPrimaryKey();
        $disk = disk()->toWork($directory);
        $uploadedFile->storeAs($disk);
        
        $file = array_merge(
            $uploadedFile->toArray(), 
            [
                'morph_type' => $entityClass::morphType(),
                'morph_id'   => $entity->getPrimaryKey(),
            ]
        );

        $repo = new static($file);
        return $repo->save() ? $repo->entity : null;
    }
}