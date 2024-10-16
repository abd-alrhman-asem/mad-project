<?php

namespace App\Http\Controllers\Api\V1;


use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderRequest;
use App\Models\Order;
use App\Services\OrderService;
use App\Traits\ApiResponseHandlerTrait;


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



    public function update(UpdateOrderRequest $request)
    {
        $order = $this->orderService->updateOrder($request->validated());

        return response()->json([
            'message' => 'order updated successfully',
            'updated order' => $order
        ]);
    }

    public function delete(Order $order)
    {
        $order = $this->orderService->deleteOrder($order);

        return response()->json([
            'message' => 'order deleted from cart.',
            'deleted order' => $order
        ]);
    }

    public function clear()
    {
        $this->orderService->clearCart();

        return response()->json([
            'message' => 'cart cleared successfully.'
        ]);
    }

//     public function store(StoreOrderRequest $request): JsonResponse
//     {
//          $this->orderService->createOrder($request->validated());
//         return $this->successMessage('Order created successfully');
//     }
}

