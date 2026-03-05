<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notification_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->string('channel')->default('whatsapp'); // whatsapp, sms, email
            $table->string('phone')->nullable();
            $table->string('type'); // status_update, invoice, reminder
            $table->string('status_trigger')->nullable(); // Which status triggered this
            $table->text('message')->nullable();
            $table->enum('delivery_status', ['sent', 'failed', 'pending'])->default('pending');
            $table->text('response')->nullable(); // API response
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_logs');
    }
};
