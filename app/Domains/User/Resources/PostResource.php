<?php

namespace App\Domains\User\Resources;

use Framework\Http\ApiResource;

class PostResource extends ApiResource
{
    public function toArray(): array
    {
        return [
            'title' => $this->entity->get('title')
        ];
    }
}