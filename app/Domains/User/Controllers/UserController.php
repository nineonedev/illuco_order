<?php

namespace App\Domains\User\Controllers;

use App\Domains\User\Repositories\UserRepository;
use App\Domains\User\Resources\UserResource;
use Framework\Routing\Controller;

class UserController extends Controller
{
    public function index()
    {
        $data = UserRepository::with(['posts'])
            ->paginate(3)
            ->toResource(UserResource::class);
        
        return $this->json($data);
    }
}