<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$kasir = \App\Models\User::updateOrCreate(
    ['email' => 'kasir@laundry.com'],
    [
        'name' => 'Kasir Cepat',
        'password' => bcrypt('password123'),
        'role' => 'kasir'
    ]
);

file_put_contents('users_dump.txt', json_encode(\App\Models\User::all()->toArray(), JSON_PRETTY_PRINT));
echo "Done";
