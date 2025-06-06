<?php 

namespace App\User\Entities;

use App\User\Repositories\PostRepository;
use Framework\Database\ORM\Entities\Entity;
use Framework\Database\ORM\Relations\BelongsTo;

class Post extends Entity
{
    protected array $fillable = [
        'user_id',
        'title',
        'body'
    ];

    protected array $casts = [
        'user_id' => 'int'
    ];

    public function repositoryClass(): string
    {
        return PostRepository::class;
    }
    
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id'); 
    }
}