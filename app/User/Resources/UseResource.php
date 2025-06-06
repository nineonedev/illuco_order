<?php

namespace App\User\Resources;

use Framework\Http\Resources\ApiResource;

class UserResource extends ApiResource
{
    public function toArray(): array
    {
        return [
            'id' => $this->entity->get('id'),
            'name' => $this->entity->get('name'),
            'email' => $this->entity->get('email'),
            'created_at' => $this->entity->get('created_at'),
        ];
    }
}
