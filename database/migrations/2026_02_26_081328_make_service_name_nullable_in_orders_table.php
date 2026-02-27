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
        Schema::table('orders', function (Blueprint $table) {
            $table->string('service_name')->nullable()->change();
            $table->decimal('weight', 8, 2)->nullable()->change();
            $table->string('package_detail')->nullable()->change();
        });

        // Migrate existing order into order_items
        $orders = \DB::table('orders')->get();
        foreach ($orders as $order) {
            if ($order->service_name) {
                // Check if already exist to prevent duplicate on rerun
                $exists = \DB::table('order_items')->where('order_id', $order->id)->exists();
                if (!$exists) {
                    \DB::table('order_items')->insert([
                        'order_id' => $order->id,
                        'service_name' => $order->service_name,
                        'qty' => $order->weight ?: 1,
                        'unit' => $order->weight ? 'Kg' : 'Pcs',
                        'price' => $order->total_price, // Assuming 1 type of service took full total_price (discounts handled separately but price could be naive)
                        'subtotal' => $order->total_price + $order->discount - $order->tax, 
                        'created_at' => $order->created_at,
                        'updated_at' => $order->updated_at,
                    ]);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Revert changes (optional)
            $table->string('service_name')->nullable(false)->change();
        });
    }
};
