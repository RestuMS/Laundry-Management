<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Setting;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class SettingController extends Controller
{
    public function index()
    {
        // Load settings to view logic
        $settings = Setting::pluck('value', 'key')->all();

        // Ensure default structure
        $defaultSettings = [
            'store_name' => 'LaundryPro',
            'store_address' => 'Jl. Sudirman No.123, Jakarta',
            'store_phone' => '081234567890',
            'receipt_footer' => 'Terima kasih telah menggunakan jasa kami.',
            'logo' => null
        ];

        foreach ($defaultSettings as $key => $default) {
            if (!isset($settings[$key])) {
                $settings[$key] = $default;
            }
        }

        return view('dashboard.settings', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'store_name' => 'required|string|max:255',
            'store_address' => 'required|string',
            'store_phone' => 'required|string|max:20',
            'receipt_footer' => 'nullable|string',
        ]);

        $keys = ['store_name', 'store_address', 'store_phone', 'receipt_footer'];

        foreach ($keys as $key) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $request->input($key)]
            );
        }

        // Handle Profile Update (Name & Password) for the logged in user
        if ($request->filled('profile_name') || $request->filled('profile_password')) {
            $user = auth()->user();
            
            $request->validate([
                'profile_name' => 'nullable|string|max:255',
                'profile_password' => 'nullable|string|min:6|confirmed',
            ]);

            if ($request->filled('profile_name')) {
                $user->name = $request->input('profile_name');
            }

            if ($request->filled('profile_password')) {
                $user->password = Hash::make($request->input('profile_password'));
            }

            $user->save();
        }

        return redirect()->back()->with('success', 'Pengaturan berhasil disimpan!');
    }
}
