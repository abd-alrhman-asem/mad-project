<?php

namespace Database\Factories;

use App\Enums\ProductType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name,
            'description' => $this->faker->text(),
            'price' => $this->faker->numberBetween(1, 1000),
            'type' => $this->faker->randomElement(array_column(ProductType::cases(), 'value')),
            'countable' => $this->faker->boolean,
            'quantity' => $this->faker->numberBetween(1, 1000),
        ];
    }
}
