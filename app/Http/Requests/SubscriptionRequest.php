<?php

// app/Http/Requests/SubscriptionRequest.php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SubscriptionRequest extends FormRequest
{
    public function rules()
    {
        return [
            'email' => 'required|email',
            'payment_method_id' => 'required|string',
            'price_id' => ['required', 'string', function ($attribute, $value, $fail) {
                $allowedPriceIds = config('services.stripe.allowed_price_ids');

                if (!in_array($value, $allowedPriceIds)) {
                    $fail('The selected price ID is invalid.');
                }
            }],
        ];
    }

    public function authorize()
    {
        return true; // Adjust according to your authorization logic
    }
}
