<?php 

namespace App\Domains\User\Entities;

use App\Domains\User\Repositories\NoticeRepository;
use Framework\Database\ORM\Entities\Entity;
use Framework\Database\ORM\Entities\HasWorkDirectory;
use Framework\Database\ORM\Entities\Morphable;

class Notice extends Entity implements HasWorkDirectory, Morphable
{
    protected array $fillable = [
        'title',
        'content',
    ];

    public static function morphType(): string
    {
        return 'notice';
    }
    
    public static function workDirectory(): string
    {
        return 'notices';
    }

    public function repositoryClass(): string
    {
        return NoticeRepository::class;
    }
}