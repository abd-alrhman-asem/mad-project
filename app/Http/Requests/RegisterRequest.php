<?php

namespace App\Http\Requests;
use App\Enums\UserStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules()
    {
        return [
            'full_name' => ['required', 'string', 'min:5'],
            'phone' => ['nullable', 'string', 'min:8', 'regex:/^[0-9]+$/'],
            'address' => ['nullable', 'string', 'min:2'],
            'governorate' => ['nullable', 'string', 'min:2'],
            'city' => ['nullable', 'string', 'min:2'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => [
                'required',
                'string',
                'min:8',
                'regex:/[a-z]/',
                'regex:/[A-Z]/',
                'regex:/[0-9]/',
                'regex:/[@$!%*?&]/',
            ],

            'photo' => ['image'],
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json($validator->errors(), 422));
    }
}
