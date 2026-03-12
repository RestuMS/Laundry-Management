<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('booking_slot_id')->nullable()->after('order_source')
                  ->constrained('booking_slots')->nullOnDelete();
            $table->date('requested_pickup_date')->nullable()->after('booking_slot_id')
                  ->comment('Tanggal antar yang diminta pelanggan');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['booking_slot_id']);
            $table->dropColumn(['booking_slot_id', 'requested_pickup_date']);
        });
    }
};
