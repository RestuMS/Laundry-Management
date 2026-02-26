<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create Admin Dummy
        \App\Models\User::updateOrCreate(
            ['email' => 'admin@laundry.com'],
            [
                'name' => 'Admin Master',
                'password' => bcrypt('password123'),
                'role' => 'admin',
            ]
        );

        // Create Kasir Dummy
        \App\Models\User::updateOrCreate(
            ['email' => 'kasir@laundry.com'],
            [
                'name' => 'Kasir Cepat',
                'password' => bcrypt('password123'),
                'role' => 'kasir',
            ]
        );

        // Create Owner Dummy
        \App\Models\User::updateOrCreate(
            ['email' => 'owner@laundry.com'],
            [
                'name' => 'Owner Bos',
                'password' => bcrypt('password123'),
                'role' => 'owner',
            ]
        );
    }
}
