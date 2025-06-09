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
        return $this->render('admin.pages.guide');
    }

    public function dashboard()
    {
        return $this->render('admin.pages.dashboard');
    }

    public function test()
    {
        return $this->render('admin.pages.test');
    }

    public function setting()
    {
        return $this->render('admin.pages.setting'); 
    }
}