<?php

namespace App\Http\Requests\Password;

use Illuminate\Foundation\Http\FormRequest;

class ResetPasswordRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Allow any user to make this request
    }

    public function rules()
    {
        return [
            'email' => 'required|email|exists:users,email',
            'verification_code' => 'required|string',
            'password' => 'required|string|min:8|confirmed', // Assuming you want password confirmation
        ];
    }

    public function messages()
    {
        return [
            'email.required' => 'The email field is required.',
            'verification_code.required' => 'The verification code field is required.',
            'password.required' => 'The password field is required.',
            'password.confirmed' => 'The password confirmation does not match.',
        ];
    }
}
