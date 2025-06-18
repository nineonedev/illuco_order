<?php

namespace App\Http\Requests\Product;

use Framework\Http\FormRequest;

class SaveProductAttributeRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'template_id' => 'required|integer',
            'name'        => 'required|string',
            'type'        => 'required|string',
            'label'       => 'required|string',
            'required'    => 'nullable|boolean',
            'sort_order'  => 'nullable|integer',
            'description' => 'nullable|string',
        ];
    }
}
