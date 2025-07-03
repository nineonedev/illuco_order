<?php

namespace App\Http\Controllers\Product;

use App\Domains\Product\Entities\Headlight;
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

        $data = [
            'options' => [
                'precison_lens' => $precisonLens ? $precisonLens->toArray() : null,
            ],
            'loupe' => Loupe::MODEL_SPECS,
            'headlight' => Headlight::MODEL_SPECS,
        ];
        
        return $this->render(null, $data, '성공적으로 로드되었습니다.');
    }
}
