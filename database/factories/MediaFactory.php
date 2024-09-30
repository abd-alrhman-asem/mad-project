<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Media>
 */
class MediaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'mediable_id' => Product::inRandomOrder()->first()->id,
            'mediable_type' =>  function (array $attributes) {
                return Product::find($attributes['mediable_id'])->getMorphClass();
            },
            'file_path' => $this->faker->imageUrl(),
            'file_type' => 'image/jpeg',
        ];
    }

    public function mediaForUser(): static
    {
        return $this->state(fn(array $attributes) => [
            'mediable_id' => User::inRandomOrder()->first()->id,
            'mediable_type' => function (array $attributes) {
                return User::find($attributes['mediable_id'])->getMorphClass();
            },
            'file_path' => $this->faker->imageUrl(),
            'file_type' => 'image/jpeg',
        ]);
    }
}
