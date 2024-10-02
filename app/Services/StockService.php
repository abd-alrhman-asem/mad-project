<?php

namespace App\Services;

use App\Models\Product;
use RuntimeException;

class StockService
{
    public function checkStock($productId, $quantity)
    {
        $product = Product::find($productId);

        if (!$product) {
            throw new RuntimeException('Product not found');
        }

        if (!$product->hasEnoughQuantity($quantity)) {
            throw new RuntimeException('Quantity is out of stock');
        }

        return $product;
    }

    public function reduceStock($product, $quantity)
    {
        $product->reduceQuantity($quantity);
    }
}

