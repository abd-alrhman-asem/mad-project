<?php

namespace App\Models;

use App\Enums\ProductType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;


    protected $fillable = [
        'name',
        'description',
        'price',
        'type',
        'countable',
        'quantity',
    ];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function media()
    {
        return $this->morphMany(Media::class, 'mediable');
    }

    protected $casts = [
        'type' => ProductType::class,
    ];

    public function hasEnoughQuantity($quantity)
    {
        return $this->quantity >= $quantity;
    }

    public function reduceQuantity($quantity)
    {
        $this->decrement('quantity', $quantity);
    }

    public function returnQuantity($quantity)
    {
        $this->increment('quantity', $quantity);
    }
}
