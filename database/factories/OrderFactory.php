<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'total_price' => $this->faker->randomFloat(2, 2000, 50000),
            'status' => $this->faker->randomElement(['completed', 'canceled']),
            'name' => $this->faker->name,
            'email' => $this->faker->safeEmail,
            'contact_number' => $this->faker->phoneNumber,
            'shipping_address' => $this->faker->address,
            'payment_method' => $this->faker->randomElement(['credit_card', 'cash_on_delivery']),
        ];
    }
}
