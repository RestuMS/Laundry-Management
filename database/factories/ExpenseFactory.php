<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ExpenseFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->randomElement(['Sabun Cuci', 'Plastik Packing', 'Listrik', 'Air PDAM', 'Pewangi', 'Gaji Karyawan']),
            'amount' => fake()->numberBetween(10000, 500000),
            'date' => fake()->dateTimeBetween('-2 months', 'now')->format('Y-m-d'),
            'note' => fake()->optional()->sentence(),
        ];
    }
}
