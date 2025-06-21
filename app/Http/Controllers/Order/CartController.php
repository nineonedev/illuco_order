<?php

namespace App\Http\Controllers\Order;

use App\Domains\Order\Repositories\CustomerRepository;
use App\Domains\Product\Repositories\ProductTemplateRepository;
use App\Services\Order\AddItemToCartService;
use Exception;
use Framework\Http\Request;
use Framework\Routing\Controller;
use Framework\Support\Collection;

class CartController extends Controller
{
    public function index()
    {
        return $this->render('admin.pages.cart.index');
    }

    public function store(Request $request)
    {
        $result = (new AddItemToCartService())->runInTransaction($request->all());
        return $result->toResponse(); 
    }
}