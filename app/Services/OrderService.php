<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use RuntimeException;
use Exception;
use App\Models\Order;
use App\Models\Product;
use App\Enums\OrderStatus;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function __construct(protected ProductService $productService)
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));
    }


    public function createOrder(array $data): Order
    {
        return DB::transaction(function () use ($data) {
            // Check if the product is in stock
            $product = $this->productService->checkStock($data['product_id'], $data['quantity']);

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
            $product = $this->productService->checkStock($data['product_id'], $data['quantity']);
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

//     public function createOrder($data): void
//     {
//         DB::transaction(function () use ($data) {
//             $product = $this->productService->checkStock($data['product_id'], $data['quantity']);

//             $data['user_id'] = auth()->id();
//             $order = Order::create($data);

//             if (!$order) {
//                 throw new RuntimeException('Order creation failed');
//             }

//             $product->reduceQuantity($data['quantity']);
//         });
//     }




    public function updateOrder($request , )
    {

        $order  = null;
        DB::transaction(function () use ($request , &$order) {

            $order = Order::findOrFail($request['order_id']);
            $product = Product::findOrFail($order->product_id);
            $old_quantity = $order->quantity;

            if(!$this->verifyOrderOwner($order)){
                throw new Exception('Not allowed to update order.');
            }

            if ($order->type == OrderStatus::Pending) {

                if (!$product->hasEnoughQuantity($request['quantity'], $old_quantity)) {
                    throw new Exception('No enough quantity.');
                }

                $order->quantity = $request['quantity'];
                $order->save();

                // $this->productService->updateProductQuantity($product, $old_quantity, $request['quantity']);
            } else {
                throw new Exception('Cannot update order.');
            }

        });
        return $order;
    }

    public function deleteOrder($order)
    {
        if (!$order) {
            throw new Exception('Order not found.');
        }

        if(!$this->verifyOrderOwner($order)){
            throw new Exception('Not allowed to delete order.');
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
         $user = Auth::user();

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

    public function verifyOrderOwner($order)
    {
        return $order->user_id == Auth::id();

    }

}

