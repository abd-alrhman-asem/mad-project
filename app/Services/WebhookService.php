<?php

namespace App\Services;

use Stripe\Webhook;
use Illuminate\Support\Facades\Log;
use App\Models\Subscription;
use App\Models\Order;
use Stripe\Stripe;
use App\Mail\ReceiptMail;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use App\Enums\SubscriptionStatus;


class WebhookService
{
    public function __construct()
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));
    }

    public function verifyWebhook($payload, $sigHeader, $endpointSecret)
    {
        return Webhook::constructEvent($payload, $sigHeader, $endpointSecret);
    }


    public function handleWebhookEvent($event)
    {
        switch ($event->type) {
                // Subscription-related events
            case 'invoice.payment_succeeded':
                $this->handleSubscriptionEvent($event, 'payment_succeeded');
                break;

            case 'invoice.payment_failed':
                $this->handleSubscriptionEvent($event, 'payment_failed');
                break;
            case 'invoice.created':
                Log::info("invoice created successfully");
                break;
            case 'invoice.paid':
                Log::info("invoice paid successfully");
                break;
            case 'invoice.finalized':
                Log::info("invoice finalized successfully");
                break;

                // Order/subscription-related events
            case 'payment_intent.succeeded': // if the payment is successful
                $paymentIntent = $event->data->object;
                if (isset($paymentIntent->metadata->order_id)) { //  if the order id is set so it's an order
                    $this->handleOrderEvent($event, 'processing');
                } else {
                    $this->handleSubscriptionEvent($event, 'succeeded');
                }
                break;
            case 'payment_intent.payment_failed': //used if the user enter the wrong card details
                $paymentIntent = $event->data->object;
                if (isset($paymentIntent->metadata->order_id)) {
                    $this->handleOrderEvent($event, 'failed');
                } else {
                    $this->handleSubscriptionEvent($event, 'failed');
                }
                break;

            case 'payment_intent.canceled': // if the payment is canceled
                $paymentIntent = $event->data->object;
                if (isset($paymentIntent->metadata->order_id)) {
                    $this->handleOrderEvent($event, 'canceled');
                } else {
                    $this->handleSubscriptionEvent($event, 'cancelled');
                }
                break;

            default:
                Log::warning('Unhandled event type: ' . $event->type);
        }
    }

    public function handleSubscriptionEvent($event, $status)
    {
        //this function used to handle the subscription events and update the subscription status depending on the event type
        $invoice = $event->data->object;

        if ($status === 'payment_succeeded') {
            $this->handleInvoicePaymentSucceeded($event);
        } elseif ($status === 'payment_failed') {
            $this->handleInvoicePaymentFailed($event);
        } elseif ($status === 'succeeded') {
            Log::info("Payment Intent succeeded");
        } elseif ($status === 'failed') {
            Log::info("Payment Intent Failed");
        } elseif ($status === 'cancelled') {
            Log::info("Payment Intent cancelled");
        }
    }

    public function handleOrderEvent($event, $statue)
    {
        // this function used to handle the order events and update the order status depending on the event type
        $paymentIntent = $event->data->object;
        Log::info("email at webhook : " . $paymentIntent->metadata->email);

        if ($event->type === 'payment_intent.succeeded') {
            Log::info('Order payment succeeded for PaymentIntent: ' . $paymentIntent->id);
            $this->updateOrderStatus($paymentIntent->id, 'processing');
            $this->sendReceipt($paymentIntent, false); // Send order receipt
        } elseif ($event->type === 'payment_intent.payment_failed') {
            Log::warning('Order payment failed for PaymentIntent: ' . $paymentIntent->id);
            $this->updateOrderStatus($paymentIntent->id, 'failed');
        } elseif ($event->type === 'payment_intent.canceled') {
            Log::warning('Order payment cancelled for PaymentIntent: ' . $paymentIntent->id);
            $this->updateOrderStatus($paymentIntent->id, 'cancelled');
        }
    }

    public function handleInvoicePaymentSucceeded($event)
    {
        // this function used to handle the invoice payment succeeded event and update the subscription status and dates
        $invoice = $event->data->object;

        try {
            $invoiceInstance = \Stripe\Invoice::retrieve($invoice->id);
            $this->sendReceipt($invoiceInstance, true); // true indicates it's a subscription
            $this->updateSubscriptionDates($invoiceInstance->subscription);
        } catch (\Exception $e) {
            Log::error("Failed to process invoice payment: " . $e->getMessage());
        }
    }


    public function handleInvoicePaymentFailed($event)
    {
        // this function used to handle the invoice payment failed event and update the subscription status
        $invoice = $event->data->object;
        $this->updateSubscriptionStatus($invoice->subscription, 'declined');
    }


    public function sendReceipt($instance, $isSubscription = true)
    {
        // this function used to send the receipt to the customer
        // check if the receipt of subscription or order
        if ($isSubscription) {
            $customerEmail = $instance->customer_email;
            $amount = number_format($instance->amount_paid / 100, 2);
            $currency = $instance->currency;

            if (empty($customerEmail)) {
                Log::error('No customer email available for subscription receipt. Cannot send receipt.');
                return;
            }

            Mail::to($customerEmail)->send(new ReceiptMail(['amount' => $amount, 'currency' => $currency]));
            Log::info("Subscription receipt sent to customer: " . $customerEmail);
        } else {
            $customerEmail = $instance->metadata->email ?? null;
            $amount = number_format($instance->amount / 100, 2);
            $currency = $instance->currency;

            if (empty($customerEmail)) {
                Log::error('No customer email available for order receipt. Cannot send receipt.');
                return;
            }

            Mail::to($customerEmail)->send(new ReceiptMail(['amount' => $amount, 'currency' => $currency]));
            Log::info("Order receipt sent to customer: " . $customerEmail);
        }
    }



    public function updateSubscriptionStatus($subscriptionId, $status)
    {
        // this function used to update the subscription status depend on the stripe_subscription_id
        Subscription::where('stripe_subscription_id', $subscriptionId)
            ->update(['type' => $status]);
        Log::info("Subscription updated to {$status}: {$subscriptionId}");
    }


    public function updateSubscriptionDates($subscriptionId)
    {
        // this function used to update the subscription dates and statue depend on the stripe_subscription_id
        $subscription = Subscription::where('stripe_subscription_id', $subscriptionId)->first();

        if ($subscription) {
            $stripeSubscription = \Stripe\Subscription::retrieve($subscriptionId);
            $price = \Stripe\Price::retrieve($stripeSubscription->items->data[0]->price->id);
            $billingCycle = $price->recurring->interval;
            $duration = $price->recurring->interval_count;

            $startDate = Carbon::now();
            Log::info(" type : " . $subscription->type->value);

            if ($subscription->type->value === SubscriptionStatus::Cancelled->value) {
                Log::info("Subscription {$subscriptionId} is cancelled. Checking for last cancelled subscription.");
                $lastCancelledSubscription = Subscription::where('user_id', $subscription->user_id)
                    ->where('type', SubscriptionStatus::Cancelled->value)
                    ->orderBy('end_date', 'desc')
                    ->first();

                if ($lastCancelledSubscription) {
                    Log::info("Found last cancelled subscription for user {$subscription->user_id}");
                    $startDate = Carbon::parse($lastCancelledSubscription->end_date);
                }
            }

            $endDate = $this->calculateEndDate($startDate, $billingCycle, $duration);

            $subscription->update([
                'type' => 'active',
                'start_date' => $startDate,
                'end_date' => $endDate,
            ]);

            Log::info("Subscription dates updated for {$subscriptionId}: start_date={$startDate}, end_date={$endDate}");
        } else {
            Log::warning("No subscription found for subscription ID: {$subscriptionId}");
        }
    }


    public function updateOrderStatus($paymentIntentId, $status)
    {
        // this function used to update the order status depend on the stripe_payment_intent_id
        $order = Order::where('stripe_payment_intent_id', $paymentIntentId)->first();

        if ($order) {
            $order->update(['type' => $status]);
            Log::info("Order updated to {$status} for PaymentIntent: {$paymentIntentId}");
        } else {
            Log::warning("No order found for PaymentIntent ID: {$paymentIntentId}");
        }
    }


    private function calculateEndDate($startDate, $billingCycle, $duration)
    {
        // this function used to calculate the end date of the subscription
        if ($billingCycle === 'month') {
            return $startDate->copy()->addMonths($duration);
        } elseif ($billingCycle === 'year') {
            return $startDate->copy()->addYears($duration);
        }

        return null;
    }
}
