<?php
namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class OrderService
{

    public function __construct(protected ProductService $stockService)
    {}

    public function createOrder($data): void
    {
        DB::transaction(function () use ($data) {
            $product = $this->stockService->checkStock($data['product_id'], $data['quantity']);

            $data['user_id'] = auth()->id();
            $order = Order::create($data);

            if (!$order) {
                throw new RuntimeException('Order creation failed');
            }

            $product->reduceQuantity($data['quantity']);
        });
    }
}
