<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->json('status_history')->nullable()->after('status');
            $table->timestamp('status_updated_at')->nullable()->after('status_history');
        });

        // Backfill existing orders with initial status history
        $orders = \App\Models\Order::withTrashed()->get();
        foreach ($orders as $order) {
            $order->status_history = [
                [
                    'status' => $order->status,
                    'changed_at' => $order->created_at->toISOString(),
                    'changed_by' => 'System (Migration)',
                ]
            ];
            $order->status_updated_at = $order->updated_at;
            $order->saveQuietly();
        }
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['status_history', 'status_updated_at']);
        });
    }
};
