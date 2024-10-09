<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\DB;
use RuntimeException;
use Stripe\Stripe;
use Stripe\PaymentIntent;

class OrderService
{
    public function __construct(protected ProductService $stockService)
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));
    }

    public function createOrder(array $data): Order
    {
        return DB::transaction(function () use ($data) {
            // Check if the product is in stock
            $product = $this->stockService->checkStock($data['product_id'], $data['quantity']);

            $data['user_id'] = auth()->id();
            $order = Order::create($data);

            if (!$order) {
                throw new RuntimeException('Order creation failed');
            }

            $product->reduceQuantity($data['quantity']);
            return $order; // Return the created order
        });
    }

    public function createPaymentIntent(array $data, int $orderId): array
    {
        return DB::transaction(function () use ($data, $orderId) {
            // Check if the product is in stock
            $product = $this->stockService->checkStock($data['product_id'], $data['quantity']);
            //create payment intent to return client secret to the frontend to complete the payment
            $paymentIntent = PaymentIntent::create([
                'amount' => $product->price * $data['quantity'] * 100, // price * quantity * 100 to convert to cents
                'currency' => 'usd',
                'description' => 'Order Payment',
                'metadata' => [
                    'order_id' => $orderId, // add order id to the metadata
                    'email' => auth()->user()->email, // add email to the metadata
                ],
                'automatic_payment_methods' => ['enabled' => true],
            ]);

            // return the client secret and payment intent ID
            return [
                'client_secret' => $paymentIntent->client_secret,
                'stripe_payment_intent_id' => $paymentIntent->id,
            ];
        });
    }
}
