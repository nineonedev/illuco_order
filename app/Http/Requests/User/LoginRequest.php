<?php

namespace App\Http\Requests\User;

use Framework\Http\FormRequest;

class LoginRequest extends FormRequest
{
    protected function rules(): array
    {
        return [
            'email'    => 'required|email|maxLength:100',
            'password' => 'required|string|minLength:8|maxLength:100',
        ];
    }

    protected function failedValidationMessage(): string
    {
        return lang('validation.login_failed');
    }
}