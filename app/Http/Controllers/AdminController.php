<?php

namespace App\Http\Controllers;

use App\Domains\User\Repositories\FileRepository;
use App\Domains\User\Repositories\UserRepository;
use Framework\Http\Request;
use Framework\Routing\Controller;

class AdminController extends Controller
{
    public function guide()
    {
        return $this->view('admin.pages.guide');
    }

    public function dashboard()
    {
        return $this->view('admin.pages.dashboard');
    }

    public function test()
    {
        return $this->view('admin.pages.test');
    }
}