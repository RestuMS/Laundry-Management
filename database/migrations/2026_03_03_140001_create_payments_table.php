<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id');
            $table->integer('amount'); // Jumlah yang dibayar
            $table->string('payment_method')->default('Cash'); // Cash, Transfer, QRIS, E-Wallet
            $table->text('note')->nullable(); // Catatan pembayaran
            $table->string('received_by')->nullable(); // Nama kasir yang menerima
            $table->timestamps();

            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->index(['order_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
