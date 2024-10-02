<?php
namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class OrderService
{
    protected StockService $stockService;

    public function __construct(StockService $stockService)
    {
        $this->stockService = $stockService;
    }

    public function createOrder($data)
    {
        DB::transaction(function () use ($data) {
            $product = $this->stockService->checkStock($data['product_id'], $data['quantity']);

            $data['user_id'] = auth()->id();
            $order = Order::create($data);

            if (!$order) {
                throw new RuntimeException('Order creation failed');
            }

            $this->stockService->reduceStock($product, $data['quantity']);
        });
    }
}
