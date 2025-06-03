<?php

namespace App\Admin\Controllers;

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

    public function signin()
    {
        return $this->view('home.pages.auth.signin');
    }

    public function login(Request $request) 
    {
        return $this->json($request->all()); 
    }

    public function logout(Request $request)
    {
        return $this->json($request->all());
    }
}