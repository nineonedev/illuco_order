<?php

namespace App\Domains\Notices\Controllers;

use Framework\Routing\Controller;

class ClaimController extends Controller
{
    public function index()
    {
        return $this->view('admin.claims.index');
    }


    public function show()
    {
        return $this->view('admin.claims.show');
    }

    public function create()
    {
        return $this->view('admin.claims.create');
    }

    
    public function edit()
    {
        return $this->view('admin.claims.edit');
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