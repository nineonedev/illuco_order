<?php 

namespace App\Http\Controllers;

use Framework\Http\Request;
use Framework\Routing\Controller;

class ProductTemplateController extends Controller
{
    public function index(Request $request)
    {
        return $this->render('admin.pages.products.templates.index');
    }

    public function create()
    {
        return $this->render('admin.pages.products.templates.create');
    }

    
    public function edit()
    {
        return $this->render('admin.pages.products.templates.edit');
    }

    public function store(Request $request)
    {
    }

    public function update()
    {
        
    }
    
    public function destroy()
    {

    }
}