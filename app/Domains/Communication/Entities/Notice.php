<?php 

namespace App\Domains\Communication\Entities;

use App\Domains\Communication\Repositories\NoticeRepository;
use Framework\Database\ORM\Entities\Entity;

class Notice extends Entity
{
    protected array $fillable = [
        'user_id',
        'title', 
        'content',
        'visible_from',
        'visible_to',
        'is_pinned',
        'status',
    ]; 

    protected array $casts = [
        'user_id' => 'int',
        'is_pinned' => 'bool',
    ];

    public static function repositoryClass(): string
    {
        return NoticeRepository::class;
    }
    
}