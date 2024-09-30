<?php

namespace Database\Factories;

use App\Enums\SubscriptionStatus;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Carbon\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Subscription>
 */
class SubscriptionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $start_date = $this->faker->date();
        $date = Carbon::parse($start_date);
        $end_date = $date->addDays(30)->format('Y-m-d');

        return [
            'user_id' => User::inRandomOrder()->first()->id,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'stripe_customer_id' => Str::random(20),
            'stripe_subscription_id' => Str::random(20),
            'type' => $this->faker->randomElement(array_column(SubscriptionStatus::cases(), 'value')),
        ];
    }
}
