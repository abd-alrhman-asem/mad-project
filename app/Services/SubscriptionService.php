<?php

namespace App\Services;

use App\Enums\SubscriptionStatus;
use Stripe\Customer;
use Stripe\PaymentMethod;
use Stripe\Subscription;
use Illuminate\Support\Facades\Log;
use App\Models\Subscription as DBSubscription;
use Carbon\Carbon;

class SubscriptionService
{
    public function __construct()
    {
        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
    }

    public function findOrCreateCustomer($userId, $email)
    {
        $existingCustomer = DBSubscription::where('user_id', $userId)->first();

        if ($existingCustomer && $existingCustomer->stripe_customer_id) {
            return $this->retrieveCustomer($existingCustomer->stripe_customer_id);
        }
        return $this->createCustomer($email);
    }

    public function retrieveCustomer($stripeCustomerId)
    {
        return Customer::retrieve($stripeCustomerId);
    }

    public function createCustomer($email)
    {
        return Customer::create(['email' => $email]);
    }

    public function attachAndSetDefaultPaymentMethod($customer, $paymentMethodId)
    {
        $paymentMethod = PaymentMethod::retrieve($paymentMethodId);
        $paymentMethod->attach(['customer' => $customer->id]);

        $this->setDefaultPaymentMethod($customer, $paymentMethod);
    }

    public function setDefaultPaymentMethod($customer, $paymentMethod)
    {

        $customer->invoice_settings = ['default_payment_method' => $paymentMethod->id];
        $customer->save();
    }

    public function hasActiveSubscription($userId)
    {
        // Use Enum for type comparison
        return DBSubscription::where('user_id', $userId)
            ->where('type', SubscriptionStatus::Active->value)
            ->exists();
    }

    public function determineStartDateAndType($userId)
    {
        $startDate = Carbon::now();
        $type = SubscriptionStatus::Pending->value;

        $canceledSubscription = DBSubscription::where('user_id', $userId)
            ->where('type', SubscriptionStatus::Cancelled->value)
            ->orderBy('end_date', 'desc')
            ->first();

        if ($canceledSubscription) {
            $type = $canceledSubscription->type;
        }

        return $type->value;
    }

    public function createSubscription($customerId, $priceId)
    {
        return Subscription::create([
            'customer' => $customerId,
            'items' => [['price' => $priceId]],
            'expand' => ['latest_invoice.payment_intent'],
        ]);
    }

    public function getDurationFromStripePrice($priceId)
    {
        try {
            $price = \Stripe\Price::retrieve($priceId);
            if ($price && isset($price->recurring)) {
                $interval = $price->recurring->interval;
                $intervalCount = $price->recurring->interval_count;

                if ($interval === 'month') {
                    return $intervalCount;
                } elseif ($interval === 'year') {
                    return $intervalCount * 12;
                }
            }
        } catch (\Exception $e) {
            Log::error("Failed to retrieve duration for price ID {$priceId}: " . $e->getMessage());
        }

        return 1; // Default to 1 month
    }

    public function saveSubscriptionToDatabase($subscription, $type, $userId)
    {
        DBSubscription::create([
            'user_id' => $userId,
            'start_date' => null,
            'end_date' => null,
            'stripe_subscription_id' => $subscription->id,
            'stripe_customer_id' => $subscription->customer,
            'type' => $type,
        ]);
    }

    public function handleSubscriptionResponse($subscription)
    {
        if ($subscription->latest_invoice && $subscription->latest_invoice->amount_due > 0) {
            return ['client_secret' => $subscription->latest_invoice->payment_intent->client_secret];
        } else {
            Log::error('No payment intent found or amount is zero for subscription: ' . $subscription->id);
            return ['error' => 'No invoice available or amount is zero'];
        }
    }

    public function unsubscribe($userId)
    {
        $subscription = DBSubscription::where('user_id', $userId)
            ->where('type', 'active')
            ->first();

        if (!$subscription) {
            return ['error' => 'No active subscription found', 'status' => 404];
        }

        try {
            Subscription::update($subscription->stripe_subscription_id, [
                'cancel_at_period_end' => true,
            ]);

            $subscription->update([
                'type' => 'cancelled'
            ]);

            return ['message' => 'Subscription canceled successfully. You will continue to benefit from it until the expiration date.', 'status' => 200];
        } catch (\Exception $e) {
            Log::error('Failed to cancel subscription: ' . $e->getMessage());
            return ['error' => 'Failed to cancel subscription', 'status' => 500];
        }
    }
}
