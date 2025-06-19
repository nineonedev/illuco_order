<?php

namespace App\Http\Requests\Product;

use Framework\Http\FormRequest;

class SaveProductAttributeRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name'          => 'required|string',
            'type'          => 'required|string',
            'label'         => 'required|string',
            'required'      => 'nullable|boolean',
            'default_value' => 'nullable|string',
            'sort_order'    => 'nullable|integer',
            'description'   => 'nullable|string',
        ];
    }

    protected function prepareForValidation(): void
    {
        if (!$this->input('required')) {
            $this->merge(['required' => 0]);
        }
    }
}
