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
            'full_name'=>['required', 'string', 'min:5'],
            'phone'=>[ 'string', 'min:8'],
            'address'=>['string', 'min:2'],
            'governorate'=>[ 'string', 'min:2'],
            'type' => ['string', Rule::in(array_column(UserStatus::cases(), 'value'))],
            'city'=>['string', 'min:2'],
            'email' => ['required', 'email',],
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
