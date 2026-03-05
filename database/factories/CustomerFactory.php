<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CustomerFactory extends Factory
{
    public function definition(): array
    {
        return [
            'full_name' => fake()->name(),
            'phone' => '08' . fake()->numerify('##########'),
            'address' => fake()->address(),
            'status' => fake()->randomElement(['Reguler', 'VIP', 'Member']),
            'total_orders' => fake()->numberBetween(0, 20),
            'notes' => fake()->optional()->sentence(),
        ];
    }

    public function reguler(): static
    {
        return $this->state(fn () => ['status' => 'Reguler']);
    }

    public function vip(): static
    {
        return $this->state(fn () => ['status' => 'VIP']);
    }

    public function member(): static
    {
        return $this->state(fn () => ['status' => 'Member']);
    }
}
