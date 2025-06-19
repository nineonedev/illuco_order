<?php

namespace App\Http\Controllers\Order;

use App\Domains\Order\Repositories\CustomerRepository;
use App\Domains\Product\Repositories\ProductTemplateRepository;
use Framework\Routing\Controller;
use Framework\Support\Collection;

class CartController extends Controller
{
    public function index()
    {
        $customers = CustomerRepository::make()->all();
        $templates = ProductTemplateRepository::make()->all();

        return $this->render('admin.pages.cart.index', [
            'customers' => array_map(function($customer){
                return [
                    'label' => "($customer->country) $customer->name", 
                    'value' => $customer->id,
                ];
            }, $customers),
            'templates' => array_map(function ($template) {
                return [
                    'label' => "($template->code) $template->name",
                    'value' => $template->id,
                ];
            }, $templates),
        ]);
    }

    public function show()
    {

    }

    public function create()
    {

    }

    public function edit()
    {

    }

    public function update()
    {

    }

    public function destroy()
    {

    }
}