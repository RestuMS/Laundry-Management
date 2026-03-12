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
use App\Http\Controllers\LandingController;

// Landing Page (Public Homepage)
Route::get('/', [LandingController::class, 'index'])->name('landing');
Route::post('/order-online', [LandingController::class, 'submitOrder'])->name('order.online')->middleware('throttle:10,1');

// Resi Tracking Public API
Route::get('/track', [\App\Http\Controllers\TrackingController::class, 'index'])->name('tracking.index');
Route::get('/api/track', [\App\Http\Controllers\TrackingController::class, 'search'])->name('tracking.search')->middleware('throttle:60,1');
// Public Complaint Submit
Route::post('/api/complaint', [\App\Http\Controllers\ComplaintController::class, 'store'])->name('complaint.store')->middleware('throttle:5,1');
// Public Rating Submit
Route::post('/api/rating', [\App\Http\Controllers\RatingController::class, 'store'])->name('rating.store')->middleware('throttle:3,1');

// Payment Integration (Midtrans)
Route::post('/api/order/{order:order_code}/pay', [\App\Http\Controllers\MidtransController::class, 'createTransaction'])->name('payment.pay');
// Midtrans Callback/Webhook
Route::post('/api/midtrans/callback', [\App\Http\Controllers\MidtransController::class, 'callback'])->name('payment.callback');

// Public: Cek ketersediaan booking slot (for landing page)
Route::get('/api/booking-slots', [\App\Http\Controllers\BookingSlotController::class, 'available'])->name('booking-slots.available')->middleware('throttle:60,1');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1');

    // Register disabled for production security — only Admin can add users via Pengguna page
    // Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    // Route::post('/register', [AuthController::class, 'register']);

    // Password Reset
    Route::get('/forgot-password', [\App\Http\Controllers\PasswordResetController::class, 'showForgotForm'])->name('password.request');
    Route::post('/forgot-password', [\App\Http\Controllers\PasswordResetController::class, 'sendResetLink'])->name('password.email')->middleware('throttle:3,1');
    Route::get('/reset-password/{token}', [\App\Http\Controllers\PasswordResetController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [\App\Http\Controllers\PasswordResetController::class, 'resetPassword'])->name('password.update');
});

// QR Code Generation (accessible without auth for invoice printing)
Route::get('/qr-code/{data}', [\App\Http\Controllers\QrCodeController::class, 'generate'])->name('qrcode.generate');

Route::middleware('auth')->group(function () {
    // Logout must be POST for CSRF protection
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    // Notifications API (All roles)
    Route::get('/api/notifications', [\App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/api/notifications/{notification}/read', [\App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/api/notifications/read-all', [\App\Http\Controllers\NotificationController::class, 'markAllRead'])->name('notifications.readAll');
    
    // Only Admin
    Route::middleware('role:admin')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');

        Route::resource('layanan', \App\Http\Controllers\ServiceController::class)->names('layanan');
        Route::resource('pengguna', \App\Http\Controllers\UserController::class)->names('pengguna');
        
        // Activity Log (Audit Trail)
        Route::get('/activity-log', [\App\Http\Controllers\ActivityLogController::class, 'index'])->name('activity-log.index');

        // Booking Slot Management (Antrian)
        Route::get('/antrian', [\App\Http\Controllers\BookingSlotController::class, 'index'])->name('booking-slots.index');
        Route::patch('/antrian/{bookingSlot}', [\App\Http\Controllers\BookingSlotController::class, 'update'])->name('booking-slots.update');
        Route::post('/antrian/bulk-toggle', [\App\Http\Controllers\BookingSlotController::class, 'bulkToggle'])->name('booking-slots.bulk-toggle');
        Route::post('/antrian/default-capacity', [\App\Http\Controllers\BookingSlotController::class, 'updateDefaultCapacity'])->name('booking-slots.default-capacity');
    });


    // Admin & Owner Shared
    Route::middleware('role:admin,owner')->group(function () {
        Route::get('/laporan', [\App\Http\Controllers\ReportController::class, 'index'])->name('laporan.index');
        Route::get('/laporan/export/pdf', [\App\Http\Controllers\ReportController::class, 'exportPdf'])->name('laporan.export.pdf');
        Route::get('/laporan/export/excel', [\App\Http\Controllers\ReportController::class, 'exportExcel'])->name('laporan.export.excel');
        
        // Expenses (Pengeluaran)
        Route::resource('pengeluaran', \App\Http\Controllers\ExpenseController::class)->only(['index', 'store', 'update', 'destroy'])->names('expense');
        
        // Inventory (Bahan Baku)
        Route::post('inventaris/{inventory}/adjust', [\App\Http\Controllers\InventoryController::class, 'adjustStock'])->name('inventory.adjust');
        Route::resource('inventaris', \App\Http\Controllers\InventoryController::class)->only(['index', 'store', 'update', 'destroy'])->parameters(['inventaris' => 'inventory'])->names('inventory');
        
        // Komplain
        Route::patch('/komplain/{complaint}/status', [\App\Http\Controllers\ComplaintController::class, 'updateStatus'])->name('complaints.update_status');
        Route::resource('komplain', \App\Http\Controllers\ComplaintController::class)->only(['index', 'destroy'])->names('complaints');
    });

    // Admin & Kasir Shared
    Route::middleware('role:admin,kasir')->group(function () {
        Route::get('order/{order}/invoice', [\App\Http\Controllers\OrderController::class, 'invoice'])->name('order.invoice');
        Route::patch('order/{order}/status', [\App\Http\Controllers\OrderController::class, 'updateStatus'])->name('order.update_status');
        Route::patch('order/{order}/payment-status', [\App\Http\Controllers\OrderController::class, 'updatePaymentStatus'])->name('order.update_payment_status');
        Route::post('order/{order}/send-wa-invoice', [\App\Http\Controllers\OrderController::class, 'sendInvoiceWa'])->name('order.send_wa_invoice');
        Route::post('order/{order}/confirm', [\App\Http\Controllers\OrderController::class, 'confirmOrder'])->name('order.confirm');
        Route::post('order/{order}/reject', [\App\Http\Controllers\OrderController::class, 'rejectOrder'])->name('order.reject');
        Route::get('order/{order}/photos', [\App\Http\Controllers\OrderController::class, 'getPhotos'])->name('order.photos.index');
        Route::post('order/{order}/photos', [\App\Http\Controllers\OrderController::class, 'uploadPhotos'])->name('order.photos.upload');
        Route::delete('order-photos/{photo}', [\App\Http\Controllers\OrderController::class, 'deletePhoto'])->name('order.photos.delete');
        Route::post('order/scan', [\App\Http\Controllers\OrderController::class, 'scanPickup'])->name('order.scan');
        Route::resource('order', \App\Http\Controllers\OrderController::class);
        
        Route::resource('pelanggan', \App\Http\Controllers\CustomerController::class)->names('pelanggan');
        
        // Payments (Pembayaran Parsial / DP)
        Route::get('order/{order}/payments', [\App\Http\Controllers\PaymentController::class, 'show'])->name('payment.show');
        Route::post('order/{order}/payments', [\App\Http\Controllers\PaymentController::class, 'store'])->name('payment.store');
        Route::delete('payments/{payment}', [\App\Http\Controllers\PaymentController::class, 'destroy'])->name('payment.destroy');
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
