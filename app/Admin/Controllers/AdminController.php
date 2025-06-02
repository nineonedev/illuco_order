<?php

namespace App\Admin\Controllers; 

class AdminController
{
    public function guide()
    {
        return view('admin.pages.guide');
    }

    public function dashboard()
    {
        return view('admin.pages.dashboard');
    }

    public function signin()
    {
        return view('home.pages.auth.signin');
    }
}