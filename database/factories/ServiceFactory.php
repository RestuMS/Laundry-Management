<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ServiceFactory extends Factory
{
    public function definition(): array
    {
        $services = [
            ['name' => 'Cuci Reguler', 'unit' => 'Kg', 'price' => 7000],
            ['name' => 'Cuci Express', 'unit' => 'Kg', 'price' => 12000],
            ['name' => 'Dry Clean', 'unit' => 'Pcs', 'price' => 25000],
            ['name' => 'Setrika Saja', 'unit' => 'Kg', 'price' => 5000],
            ['name' => 'Cuci Sepatu', 'unit' => 'Pcs', 'price' => 35000],
        ];

        $svc = fake()->randomElement($services);

        return [
            'service_name' => $svc['name'] . ' ' . fake()->unique()->numerify('##'),
            'icon' => '🧺',
            'description' => fake()->sentence(),
            'price' => $svc['price'],
            'unit' => $svc['unit'],
        ];
    }
}
