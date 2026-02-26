<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_code')->unique();
            $table->string('customer_name');
            $table->string('customer_phone')->nullable();
            $table->string('service_name');
            $table->decimal('weight', 8, 2)->nullable();
            $table->string('package_detail')->nullable(); // e.g '5 Kg', 'Kemeja', 'Jas'
            $table->dateTime('estimated_finish')->nullable();
            $table->string('status')->default('Diterima'); // Diterima, Dicuci, Dikeringkan, Disetrika, Quality Control, Selesai, Diambil
            $table->integer('total_price');
            $table->integer('discount')->default(0);
            $table->integer('tax')->default(0);
            $table->string('payment_method')->nullable(); // Cash, Transfer
            $table->string('payment_status')->default('Belum Bayar'); // Belum Bayar, DP, Lunas
            $table->text('notes')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
