<?php

namespace App\Http\Requests;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ResendNotificationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules()
    {
        return [
            'email' => ['required', 'string', 'email', 'exists:users,email'],

        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw ValidationException::withMessages($validator->errors()->toArray());
    }
}
