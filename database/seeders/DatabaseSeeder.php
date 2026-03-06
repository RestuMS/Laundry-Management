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
                'password' => 'password123',
                'role' => 'admin',
            ]
        );

        // Create Kasir Dummy
        \App\Models\User::updateOrCreate(
            ['email' => 'kasir@laundry.com'],
            [
                'name' => 'Kasir Cepat',
                'password' => 'password123',
                'role' => 'kasir',
            ]
        );

        // Create Owner Dummy
        \App\Models\User::updateOrCreate(
            ['email' => 'owner@laundry.com'],
            [
                'name' => 'Owner Bos',
                'password' => 'password123',
                'role' => 'owner',
            ]
        );

        // Seed Default Services
        \App\Models\Service::firstOrCreate(
            ['service_name' => 'Cuci Setrika (Reguler)'],
            [
                'description' => 'Cuci pakaian hingga bersih, wangi, dan disetrika rapi (Reguler 2-3 Hari).',
                'price' => 7000,
                'unit' => 'Kg',
                'icon' => null
            ]
        );

        \App\Models\Service::firstOrCreate(
            ['service_name' => 'Cuci Lipat (Express)'],
            [
                'description' => 'Cuci dan lipat rapi tanpa disetrika. Selesai super cepat dalam 1 hari.',
                'price' => 6000,
                'unit' => 'Kg',
                'icon' => null
            ]
        );

        \App\Models\Service::firstOrCreate(
            ['service_name' => 'Cuci Satuan (Jas/Blazer)'],
            [
                'description' => 'Cuci khusus bahan jas atau blazer (Dry Cleaning). Bebas kusut dan anti luntur.',
                'price' => 25000,
                'unit' => 'Pcs',
                'icon' => null
            ]
        );

        \App\Models\Service::firstOrCreate(
            ['service_name' => 'Cuci Setrika Selimut/Bedcover'],
            [
                'description' => 'Mencuci selimut atau bedcover segala jenis bahan.',
                'price' => 30000,
                'unit' => 'Pcs',
                'icon' => null
            ]
        );
    }
}
