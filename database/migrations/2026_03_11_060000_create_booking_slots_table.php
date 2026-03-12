<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('booking_slots', function (Blueprint $table) {
            $table->id();
            $table->date('booking_date')->unique()->comment('Tanggal slot reservasi');
            $table->decimal('max_capacity_kg', 8, 1)->default(50)->comment('Kapasitas maksimal kg per hari');
            $table->decimal('booked_kg', 8, 1)->default(0)->comment('Total kg yang sudah dipesan');
            $table->boolean('is_open')->default(true)->comment('Apakah slot ini dibuka untuk booking');
            $table->text('notes')->nullable()->comment('Catatan internal (misal: tutup hari raya)');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_slots');
    }
};
