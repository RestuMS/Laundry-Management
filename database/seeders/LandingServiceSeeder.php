<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Service;

class LandingServiceSeeder extends Seeder
{
    public function run(): void
    {
        // Delete all old services
        Service::truncate();

        $services = [
            [
                'service_name' => 'Cuci + Setrika Reguler',
                'description'  => 'Pakaian dicuci bersih, dikeringkan, dan disetrika rapi (2-3 hari).',
                'price'        => 6000,
                'unit'         => 'Kg',
                'icon'         => 'satuan.png',
            ],
            [
                'service_name' => 'Setrika Saja',
                'description'  => 'Biarkan kami yang merapikan pakaian Anda. Wangi dan anti kusut (1 hari).',
                'price'        => 4000,
                'unit'         => 'Kg',
                'icon'         => 'setrika.png',
            ],
            [
                'service_name' => 'Cuci Express 1 Hari',
                'description'  => 'Butuh cepat? Cucian Anda kami prioritaskan selesai dalam 24 jam.',
                'price'        => 10000,
                'unit'         => 'Kg',
                'icon'         => 'satuan.png',
            ],
            [
                'service_name' => 'Bedcover & Selimut',
                'description'  => 'Perawatan khusus untuk bedcover, selimut tebal, dan sprei agar tetap lembut.',
                'price'        => 25000,
                'unit'         => 'Pcs',
                'icon'         => 'selimut.png',
            ],
            [
                'service_name' => 'Cuci Sepatu Premium',
                'description'  => 'Membersihkan semua jenis sepatu: Sneakers, Kanvas, Kulit sampai seperti baru.',
                'price'        => 20000,
                'unit'         => 'Pasang',
                'icon'         => 'sepatu.png',
            ],
            [
                'service_name' => 'Dry Clean Jas / Gaun',
                'description'  => 'Pakaian formal Anda dicuci kering tanpa merusak bahan kain dan bentuknya.',
                'price'        => 35000,
                'unit'         => 'Pcs',
                'icon'         => 'jas.png',
            ]
        ];

        foreach ($services as $service) {
            Service::create($service);
        }
    }
}
