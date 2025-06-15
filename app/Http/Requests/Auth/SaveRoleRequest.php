<?php

namespace App\Http\Requests\Auth;

use Framework\Http\FormRequest;

class SaveRoleRequest extends FormRequest
{
    protected function rules(): array
    {
        return [
            'name' => 'required|string',
            'permissions' => 'array'
        ];
    }
}