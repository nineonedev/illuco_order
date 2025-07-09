<?php

namespace App\Http\Requests\User;

use Framework\Http\FormRequest;
use Framework\Support\Facades\Hash;
use Framework\Validation\Validator;

class UpdateUserRequest extends FormRequest
{
    protected function rules(): array
    {
        return [
            'name'     => 'required|string|maxLength:50',
            'email'    => 'required|email|maxLength:100',
            'phone'    => 'nullable|string|maxLength:20',
        ];
    }

    protected function afterValidation(): void
    {
        if ($this->input('password')) {
            $validator = Validator::make([
                'password' => $this->input('password'),
            ], [
                'password' => 'required|string|minLength:8|maxLength:100'
            ]);

            $validator->validateOrFail();

            $this->setValidated([
                'password' => Hash::make($this->input('password')),
            ]);
        } 
    }
}