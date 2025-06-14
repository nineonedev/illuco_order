<?php

namespace App\Http\Controllers;

use Framework\Http\Request;
use Framework\Routing\Controller;

class NoticeController extends Controller
{
    public function index(Request $request)
    {
        return $this->render('admin.pages.notices.index');
    }


    public function show(string $id)
    {
        return $this->render('admin.pages.notices.show', ['id'=> $id]);
    }

    public function create()
    {
        return $this->render('admin.pages.notices.create');
    }

    
    public function edit()
    {
        return $this->render('admin.pages.notices.edit');
    }

    public function store()
    {

    }

    public function update()
    {
        
    }
    
    public function destroy()
    {

    }
}