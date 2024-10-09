<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderRequest;
use App\Services\OrderService;
use App\Traits\ApiResponseHandlerTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    use ApiResponseHandlerTrait;

    public function __construct(protected OrderService $orderService) {}

    public function store(StoreOrderRequest $request)
    {
        //create the order
        $order = $this->orderService->createOrder($request->validated());

        //create the payment intent with the order id and data
        $paymentIntentData = $this->orderService->createPaymentIntent([
            'product_id' => $request->input('product_id'),
            'quantity' => $request->input('quantity'),
        ], $order->id);

        $order->stripe_payment_intent_id = $paymentIntentData['stripe_payment_intent_id'];
        $order->save(); // save the stripe_payment_intent_id to the order table

        // return the order id and client secret to the frontend
        return $this->successResponse([
            'order_id' => $order->id,
            'client_secret' => $paymentIntentData['client_secret'],
            'stripe_payment_intent_id' => $paymentIntentData['stripe_payment_intent_id'],
        ], 'Order created successfully');
    }
}
