<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Validation\ValidationException;

class OrderService
{
    /**
     * @throws ValidationException
     */
    public function createOrder($data)
    {
        $product = Product::find($data['product_id']);

        if (!$product->hasEnoughQuantity($data['quantity'])) {
            throw ValidationException::withMessages([
                'quantity' => 'الكمية المطلوبة غير متاحة.',
            ]);
        }

        $order = Order::create($data);

//        dd($order);
        $product->reduceQuantity($data['quantity']);
        return $order;
    }
}
