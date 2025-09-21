<?php

namespace App\Http\Requests\Product;

use Exception;
use Framework\Http\FormRequest;

class SaveProductTemplateRequest extends FormRequest
{
    protected function rules(): array
    {
        $id = route_param('id');

        return [
            'category_id'  => 'nullable|integer|exists:product_categories,id',
            'name'         => 'required|string|maxLength:50',
            'code'         => 'required|string',
            'model'        => 'required|string|maxLength:50|unique:product_templates,model' . ($id ? ",{$id}" : ''),
            'sort_order'   => 'nullable|integer',
            'description'  => 'nullable|string',
            'serial_abbr'     => 'nullable|string|maxLength:16',
            'serial_special'  => 'nullable|string|minLength:1|maxLength:3',
            'serial_revision' => 'nullable|string|minLength:1|maxLength:3',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
