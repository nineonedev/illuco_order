<?php

namespace App\Http\Requests\User;

use Framework\Http\FormRequest;
use Framework\Support\Facades\Hash;

class RegisterFormRequest extends FormRequest
{
    protected function rules(): array
    {
        return [
            'name'     => 'required|string|maxLength:50',
            'email'    => 'required|email|maxLength:100|unique:users',
            'password' => 'required|string|minLength:8|maxLength:100',
        ];
    }

    protected function afterValidation(): void
    {
        $this->setValidated([
            'password' => Hash::make($this->input('password')),
        ]);
    }

    protected function failedValidationMessage(): string
    {
        return lang('validation.register_failed');
    }
}