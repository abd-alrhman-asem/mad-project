<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\OrderService;
use App\Http\Requests\UpdateOrderRequest;

class OrderController extends Controller
{
    public function __construct(protected OrderService $orderService){}

    public function update(UpdateOrderRequest $request)
    {
        $order = $this->orderService->updateOrder($request->validated());

        return response()->json([
            'messaage' => 'order updated successfully',
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
}
