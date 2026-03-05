<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('in_app_notifications', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // order_new, order_status, payment, stock_low, system
            $table->string('title');
            $table->text('message');
            $table->string('icon')->nullable(); // phosphor icon class
            $table->string('color')->default('blue'); // blue, green, red, orange, purple
            $table->string('link')->nullable(); // URL to navigate to
            $table->unsignedBigInteger('order_id')->nullable();
            $table->string('for_role')->nullable(); // admin, kasir, owner, null = all
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->index(['for_role', 'is_read', 'created_at']);
            $table->index('order_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('in_app_notifications');
    }
};
