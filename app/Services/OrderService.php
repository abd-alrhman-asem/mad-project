<?php

namespace App\Services;

use Exception;
use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use App\Enums\OrderStatus;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class OrderService
{
    public function updateOrder($request)
    {
        $order = Order::findOrFail($request['order_id']);
        $old_quantity = $order->quantity;

        if ($order->type == OrderStatus::Pending) {
            $product = Product::findOrFail($order->product_id);

            $product->returnQuantity($old_quantity);
            $product->save();

            if (!$product->hasEnoughQuantity($request['quantity'])) {
                throw new Exception('No enough quantity.');
            }
            $order->quantity = $request['quantity'];
            $order->save();

            $product->reduceQuantity($request['quantity']);
        }

        return $order;
    }

    public function deleteOrder($order)
    {
        if (!$order) {
            throw new Exception('Order not found.');
        }

        $product = Product::findOrfail($order->product_id);

        if ($order->type = OrderStatus::Pending) {
            $product->returnQuantity($order->quantity);
            $product->save();
            $order->delete();
        }

        return $order;
    }

    public function clearCart()
    {
        // $user = Auth::user();
        $user = User::first();
        $pendingOrders = $user->pendingOrders();

        if ($pendingOrders->isEmpty()) {
            throw new Exception('No orders in your cart!');
        }

        foreach ($pendingOrders as $order) {
            $product = Product::findOrFail($order->product_id);
            $product->returnQuantity($order->quantity);
            $product->save();
            $order->delete();
        }
    }
}
