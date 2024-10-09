<?php

namespace App\Services;

class ProductService
{
    public function updateProductQuantity($product, $quantityToReturn, $quantityToReduce)
    {
        $product->returnQuantity($quantityToReturn);
        $product->reduceQuantity($quantityToReduce);
        $product->save();

        return $product;
    }
}
