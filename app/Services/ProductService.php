<?php

namespace App\Services;

class ProductService
{
    public function returnQuantityToProduct($product, $quantity)
    {
        $product->returnQuantity($quantity);
        $product->save();

        return $product;
    }

    public function reduceQuantityfromProduct($product, $quantity)
    {
        $product->reduceQuantity($quantity);
        $product->save();

        return $product;
    }
}
