<?php

namespace App\Http\Controllers\Api\V1\OnlinePayment;

use App\Http\Controllers\Controller;
use App\Http\Requests\SubscriptionRequest;
use App\Services\SubscriptionService;
use App\Enums\SubscriptionStatus; // Import the Enum
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SubscriptionController extends Controller
{
    protected $subscriptionService;

    public function __construct(SubscriptionService $subscriptionService)
    {
        $this->subscriptionService = $subscriptionService;
    }

    public function subscribe(SubscriptionRequest $request)
    {
        try {
            $customer = $this->subscriptionService->findOrCreateCustomer(auth()->id(), $request->email);
            $this->subscriptionService->attachAndSetDefaultPaymentMethod($customer, $request->payment_method_id);

            if ($this->subscriptionService->hasActiveSubscription(auth()->id())) {
                return response()->json([
                    'error' => 'You must cancel your current subscription before subscribing to a new one.'
                ], 403);
            }

            $type = $this->subscriptionService->determineStartDateAndType(auth()->id());
            Log::info("Type: " . $type);

            $subscription = $this->subscriptionService->createSubscription($customer->id, $request->price_id);

            $duration = $this->subscriptionService->getDurationFromStripePrice($request->price_id);


            // Use Enum for type
            $this->subscriptionService->saveSubscriptionToDatabase($subscription, $type, auth()->id());

            return $this->subscriptionService->handleSubscriptionResponse($subscription);
        } catch (\Exception $e) {
            Log::error('Subscription creation failed: ' . $e->getMessage());
            return response()->json(['error' => 'Subscription creation failed'], 500);
        }
    }
}
