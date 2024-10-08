<?php

namespace App\Services;

use Exception;
use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use App\Enums\OrderStatus;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function __construct(protected ProductService $productService){}

    public function updateOrder($request)
    {
        DB::transaction(function () use ($request) {

            $order = Order::findOrFail($request['order_id']);
            $product = Product::findOrFail($order->product_id);
            $old_quantity = $order->quantity;

            if ($order->type == OrderStatus::Pending) {

                if (!$product->hasEnoughQuantity($request['quantity'])) {
                    throw new Exception('No enough quantity.');
                } else {
                    $order->quantity = $request['quantity'];
                    $order->save();

                    $this->productService->returnQuantityToProduct($product, $old_quantity);
                    $this->productService->reduceQuantityfromProduct($product, $request['quantity']);
                }
            }

            return $order;
        });

        throw new Exception("Cannot update order.");
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
