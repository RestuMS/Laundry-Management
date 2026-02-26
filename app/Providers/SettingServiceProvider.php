<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class SettingServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        if (\Illuminate\Support\Facades\Schema::hasTable('settings')) {
            $settings = \App\Models\Setting::pluck('value', 'key')->all();

            $globalSettings = [
                'store_name' => $settings['store_name'] ?? 'LaundryPro',
                'store_address' => $settings['store_address'] ?? 'Jl. Sudirman No.123, Jakarta',
                'store_phone' => $settings['store_phone'] ?? '081234567890',
                'receipt_footer' => $settings['receipt_footer'] ?? 'Terima kasih telah menggunakan jasa kami.',
            ];

            \Illuminate\Support\Facades\View::share('globalSettings', $globalSettings);
        }
    }
}
