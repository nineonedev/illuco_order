<?php

namespace App\Post\Entities;

use App\User\Entities\User;
use Framework\Database\Entities\Entity;
use Framework\Database\Relations\BelongsTo;

class Post extends Entity
{
    protected $fillable = [
        'title',
        'content'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class,'user_id');
    }
}