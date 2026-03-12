<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Payment;
use Midtrans\Config;
use Midtrans\Snap;
use Illuminate\Support\Facades\Log;

class MidtransController extends Controller
{
    public function __construct()
    {
        Config::$serverKey = config('services.midtrans.server_key');
        Config::$isProduction = config('services.midtrans.is_production');
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }

    public function createTransaction(Order $order)
    {
        // Cancel if order is already Lunas completely
        if ($order->remaining_balance <= 0) {
            return response()->json(['success' => false, 'message' => 'Pesanan sudah lunas.']);
        }

        // Return existing snap token if one exists and we haven't changed the total
        // But for simplicity, we'll re-generate if we don't have one
        if ($order->snap_token) {
            return response()->json(['success' => true, 'snap_token' => $order->snap_token]);
        }

        $params = array(
            'transaction_details' => array(
                'order_id' => $order->order_code . '-' . time(), // Appending time to avoid duplicate order_id in Midtrans if we recreate
                'gross_amount' => $order->remaining_balance,
            ),
            'customer_details' => array(
                'first_name' => collect(explode(' ', $order->customer_name))->first(),
                'last_name' => collect(explode(' ', $order->customer_name))->slice(1)->implode(' '),
                'phone' => $order->customer_phone,
            ),
        );

        try {
            $snapToken = Snap::getSnapToken($params);
            $order->update(['snap_token' => $snapToken]);

            return response()->json(['success' => true, 'snap_token' => $snapToken]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function callback(Request $request)
    {
        $serverKey = config('services.midtrans.server_key');
        $hashed = hash("sha512", $request->order_id.$request->status_code.$request->gross_amount.$serverKey);

        if ($hashed == $request->signature_key) {
            if ($request->transaction_status == 'capture' || $request->transaction_status == 'settlement') {
                $orderCode = explode('-', $request->order_id)[0];
                $order = Order::where('order_code', $orderCode)->first();

                if ($order) {
                    $paidAmount = (int)$request->gross_amount;
                    
                    // Create payment record
                    Payment::create([
                        'order_id' => $order->id,
                        'amount' => $paidAmount,
                        'payment_method' => strtoupper($request->payment_type),
                    ]);

                    // Determine new payment status based on total paid vs grand total
                    $totalPaid = $order->total_paid; // Accessor recalculates it
                    $status = 'DP';
                    if ($totalPaid >= $order->grand_total) {
                        $status = 'Lunas';
                        $order->snap_token = null; // Clear token since it's fully paid
                    }

                    $order->update([
                        'payment_status' => $status
                    ]);
                }
            }
        }
    }
}
