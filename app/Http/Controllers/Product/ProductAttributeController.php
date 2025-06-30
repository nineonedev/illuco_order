<?php

namespace App\Http\Controllers\Product;

use App\Domains\Product\Entities\Loupe;
use Framework\Routing\Controller;

class ProductAttributeController extends Controller
{
    public function index()
    {
        return $this->render(null, [
            'loupe' => Loupe::$modelSpecs,
            
        ], '성공적으로 로드되었습니다.');
    }
}
