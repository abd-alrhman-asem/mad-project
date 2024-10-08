<?php

namespace App\Services;

use RuntimeException;
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

                if (!$product->hasEnoughQuantity($request['quantity'], $old_quantity)) {
                    throw new Exception('No enough quantity.');
                } else {
                    $order->quantity = $request['quantity'];
                    $order->save();

                    // $this->productService->updateProductQuantity($product, $old_quantity, $request['quantity']); 
                }
            } else {
                throw new Exception('Cannot update order.');
            }

            return $order;
        });
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
      
      
    public function createOrder($data): void
    {
        DB::transaction(function () use ($data) {
            $product = $this->productService->checkStock($data['product_id'], $data['quantity']);

            $data['user_id'] = auth()->id();
            $order = Order::create($data);

            if (!$order) {
                throw new RuntimeException('Order creation failed');
            }

            $product->reduceQuantity($data['quantity']);
        });
    }
    }

}
