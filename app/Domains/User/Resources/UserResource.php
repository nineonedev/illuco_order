<?php

namespace App\Domains\User\Resources;

use Framework\Http\ApiResource;

class UserResource extends ApiResource
{
    public function toArray(): array
    {
        return [
            'id' => $this->entity->get('id'),
            'name' => $this->entity->get('name'),
            'email' => $this->entity->get('email'),
            'created_at' => $this->entity->get('created_at'),
            'posts' => PostResource::collection($this->entity->get('posts')),
        ];
    }
}
