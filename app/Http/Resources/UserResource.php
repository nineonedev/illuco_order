<?php

namespace App\Http\Resources;

use Framework\Http\ApiResource;

class UserResource extends ApiResource
{
    public function toArray(): array
    {
        return [
            'name' => $this->entity->get('name'),
            'email' => $this->entity->get('email'),
            'created_at' => $this->entity->get('created_at'),
        ];
    }
}
