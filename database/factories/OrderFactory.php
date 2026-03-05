<?php

namespace Database\Factories;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    public function definition(): array
    {
        return [
            'order_code' => 'ORD-' . strtoupper(uniqid()),
            'customer_id' => Customer::factory(),
            'customer_name' => fake()->name(),
            'customer_phone' => '08' . fake()->numerify('##########'),
            'estimated_finish' => now()->addDays(2),
            'status' => 'Diterima',
            'total_price' => fake()->numberBetween(20000, 200000),
            'discount' => 0,
            'tax' => 0,
            'payment_method' => fake()->randomElement(['Cash', 'Transfer', 'E-Wallet']),
            'payment_status' => fake()->randomElement(['Belum Bayar', 'DP', 'Lunas']),
            'notes' => fake()->optional()->sentence(),
        ];
    }

    public function diterima(): static
    {
        return $this->state(fn () => ['status' => 'Diterima']);
    }

    public function selesai(): static
    {
        return $this->state(fn () => ['status' => 'Selesai']);
    }

    public function diambil(): static
    {
        return $this->state(fn () => ['status' => 'Diambil']);
    }

    public function lunas(): static
    {
        return $this->state(fn () => ['payment_status' => 'Lunas']);
    }
}
