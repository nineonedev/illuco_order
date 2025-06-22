<?php 

namespace App\Domains\Product\Entities;

use App\Domains\Product\Repositories\CategoryRepository;
use Framework\Database\ORM\Entities\Entity;
use Framework\Database\ORM\Traits\SoftDeletes;

class Category extends Entity
{
    use SoftDeletes;
    
    protected array $fillable = [
        
    ];

    protected array $casts = [
        
    ];

    public static function repositoryClass(): string
    {
        return CategoryRepository::class;
    }
}