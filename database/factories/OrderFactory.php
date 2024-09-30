<?php

namespace Database\Factories;

use App\Enums\OrderStatus;
use App\Models\User;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $product = Product::inRandomOrder()->first();

        return [
            'user_id' => User::inRandomOrder()->first()->id,
            'product_id' => $product->id,
            'quantity' => $this->faker->numberBetween(1, $product->quantity),
            'type' => $this->faker->randomElement(array_column(OrderStatus::cases(), 'value')),
        ];
    }
}
