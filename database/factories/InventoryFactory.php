<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class InventoryFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->randomElement(['Deterjen Cair', 'Pewangi', 'Pemutih', 'Pelembut', 'Plastik 30x40', 'Hanger']),
            'stock' => fake()->randomFloat(2, 1, 100),
            'unit' => fake()->randomElement(['Liter', 'Kg', 'Pcs', 'Pack']),
            'usage_per_kg' => fake()->randomFloat(3, 0, 0.1),
            'minimum_stock' => fake()->numberBetween(2, 10),
        ];
    }
}
