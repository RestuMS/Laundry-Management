<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

use App\Http\Controllers\AuthController;

Route::get('/', function () {
    return redirect()->route('tracking.index');
});

// Resi Tracking Public API
Route::get('/track', [\App\Http\Controllers\TrackingController::class, 'index'])->name('tracking.index');
Route::get('/api/track', [\App\Http\Controllers\TrackingController::class, 'search'])->name('tracking.search')->middleware('throttle:60,1');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::middleware('auth')->group(function () {
    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
    
    // Only Admin
    Route::middleware('role:admin')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');

        Route::resource('layanan', \App\Http\Controllers\ServiceController::class)->names('layanan');
        Route::resource('pengguna', \App\Http\Controllers\UserController::class)->names('pengguna');
    });

    // Admin & Owner Shared
    Route::middleware('role:admin,owner')->group(function () {
        Route::get('/laporan', [\App\Http\Controllers\ReportController::class, 'index'])->name('laporan.index');
    });

    // Admin & Kasir Shared
    Route::middleware('role:admin,kasir')->group(function () {
        Route::get('order/{order}/invoice', [\App\Http\Controllers\OrderController::class, 'invoice'])->name('order.invoice');
        Route::patch('order/{order}/status', [\App\Http\Controllers\OrderController::class, 'updateStatus'])->name('order.update_status');
        Route::resource('order', \App\Http\Controllers\OrderController::class);
        
        Route::resource('pelanggan', \App\Http\Controllers\CustomerController::class)->names('pelanggan');
    });

    // Only Kasir
    Route::middleware('role:kasir')->group(function () {
        Route::get('/kasir', [\App\Http\Controllers\DashboardController::class, 'kasir'])->name('kasir');
    });

    // Only Owner
    Route::middleware('role:owner')->group(function () {
        Route::get('/owner', [\App\Http\Controllers\DashboardController::class, 'owner'])->name('owner');
    });

    // Profile & Settings
    Route::get('/pengaturan', [\App\Http\Controllers\SettingController::class, 'index'])->name('settings.index');
    Route::post('/pengaturan', [\App\Http\Controllers\SettingController::class, 'update'])->name('settings.update');
});
