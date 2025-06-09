<?php 

namespace App\Domains\Notice\Entities;

use App\Domains\Notice\Repositories\NoticeRepository;
use Framework\Database\ORM\Entities\Entity;

class Notice extends Entity
{
    protected array $fillable = []; 

    public static function repositoryClass(): string
    {
        return NoticeRepository::class;
    }
}