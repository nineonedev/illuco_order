<?php

namespace App\Http\Requests\Product;

use Framework\Http\FormRequest;

class SaveOptionRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'attribute_id'  => 'required|integer|exists:product_attributes,id',
            'label'         => 'required|string|maxLength:255',
            'value'         => 'required|string|maxLength:255',
            'sort_order'    => 'nullable|integer',
        ];
    }
}
