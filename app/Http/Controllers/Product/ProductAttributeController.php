<?php

namespace App\Http\Controllers\Product;

use App\Domains\Product\Entities\Loupe;
use App\Domains\Product\Repositories\ProductTemplateRepository;
use Framework\Routing\Controller;

class ProductAttributeController extends Controller
{
    public function index()
    {
        $precisonLens = ProductTemplateRepository::make()
            ->query()
            ->with(['fileattachment'])
            ->where('model', 'PR-LENS-30')
            ->first();

        return $this->render(null, [
            'options' => [
                'precison_lens' => $precisonLens ? $precisonLens->toArray() : null,
            ],
            'loupe' => Loupe::$modelSpecs,
            
        ], '성공적으로 로드되었습니다.');
    }
}
