<?php 

namespace App\Domains\User\Entities;

use App\Domains\User\Repositories\PostRepository;
use Framework\Database\ORM\Entities\Entity;
use Framework\Database\ORM\Entities\HasWorkDirectory;
use Framework\Database\ORM\Entities\Morphable;

class Post extends Entity implements HasWorkDirectory, Morphable
{
    protected array $fillable = [
        'user_id',
        'title',
    ];

    protected array $casts = [
        'user_id' => 'int',
    ];

    public static function morphType(): string
    {
        return 'post';
    }
    
    public static function workDirectory(): string
    {
        return 'posts';
    }

    public function repositoryClass(): string
    {
        return PostRepository::class;
    }
}