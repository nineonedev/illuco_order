<?php

namespace App\Http\Controllers;

use Framework\Http\Request;
use Framework\Routing\Controller;

class ClaimController extends Controller
{
    public function index(Request $request)
    {
        return $this->render('admin.pages.claims.index');
    }


    public function show(string $id)
    {
        return $this->render('admin.pages.claims.show', ['id'=> $id]);
    }

    public function create()
    {
        return $this->render('admin.pages.claims.create');
    }

    
    public function edit()
    {
        return $this->render('admin.pages.claims.edit');
    }

    public function store(Request $request)
    {
        return $this->render(null, $request->all());
    }

    public function update()
    {
        
    }
    
    public function destroy()
    {

    }
}