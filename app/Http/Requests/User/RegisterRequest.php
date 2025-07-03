<?php

namespace App\Http\Requests\User;

use Framework\Http\FormRequest;
use Framework\Support\Facades\Hash;

class RegisterRequest extends FormRequest
{
    protected function rules(): array
    {
        return [
            'name'     => 'required|string|maxLength:50',
            'email'    => 'required|email|maxLength:100|unique:users',
            'password' => 'required|string|minLength:8|maxLength:100',
            'gender' => 'nullable|string',
            'birth' => 'nullable|date',
            'phone' => 'nullable|string',
        ];
    }

    protected function afterValidation(): void
    {
        $this->setValidated([
            'password' => Hash::make($this->input('password')),
        ]);
    }
}