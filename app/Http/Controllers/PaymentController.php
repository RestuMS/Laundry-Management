<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Payment;
use App\Models\ActivityLog;
use App\Models\InAppNotification;

class PaymentController extends Controller
{
    /**
     * Show payment history for an order
     */
    public function show(Order $order)
    {
        $payments = $order->payments()->latest()->get();
        
        return response()->json([
            'order_code' => $order->order_code,
            'customer_name' => $order->customer_name,
            'grand_total' => $order->grand_total,
            'total_paid' => $order->total_paid,
            'remaining' => $order->remaining_balance,
            'payment_status' => $order->payment_status,
            'payments' => $payments->map(fn($p) => [
                'id' => $p->id,
                'amount' => $p->amount,
                'payment_method' => $p->payment_method,
                'note' => $p->note,
                'received_by' => $p->received_by,
                'created_at' => $p->created_at->format('d/m/Y H:i'),
            ]),
        ]);
    }

    /**
     * Store a new payment for an order
     */
    public function store(Request $request, Order $order)
    {
        $request->validate([
            'amount' => 'required|integer|min:1',
            'payment_method' => 'required|string|in:Cash,Transfer,QRIS,E-Wallet',
            'note' => 'nullable|string|max:255',
        ]);

        $amount = $request->amount;
        $remaining = $order->remaining_balance;

        // Prevent overpayment
        if ($amount > $remaining) {
            return response()->json([
                'success' => false,
                'message' => 'Jumlah pembayaran melebihi sisa tagihan (Rp ' . number_format($remaining, 0, ',', '.') . ').'
            ], 422);
        }

        $payment = Payment::create([
            'order_id' => $order->id,
            'amount' => $amount,
            'payment_method' => $request->payment_method,
            'note' => $request->note,
            'received_by' => auth()->user()->name,
        ]);

        // Recalculate remaining
        $newTotalPaid = $order->payments()->sum('amount');
        $grandTotal = $order->grand_total;
        $newRemaining = $grandTotal - $newTotalPaid;

        // Auto-update payment status
        if ($newRemaining <= 0) {
            $order->update(['payment_status' => 'Lunas']);
        } elseif ($newTotalPaid > 0 && $order->payment_status === 'Belum Bayar') {
            $order->update(['payment_status' => 'DP']);
        }

        // Log activity
        ActivityLog::log('payment', "Pembayaran Rp " . number_format($amount, 0, ',', '.') . " diterima untuk order {$order->order_code}", $order);

        // In-app notification
        InAppNotification::notify(
            'payment',
            'Pembayaran Diterima',
            "Pembayaran Rp " . number_format($amount, 0, ',', '.') . " untuk order {$order->order_code} ({$order->customer_name})",
            [
                'order_id' => $order->id,
                'link' => route('order.index'),
                'for_role' => null, // all roles
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Pembayaran berhasil dicatat!',
            'payment' => [
                'id' => $payment->id,
                'amount' => $payment->amount,
                'payment_method' => $payment->payment_method,
                'note' => $payment->note,
                'received_by' => $payment->received_by,
                'created_at' => $payment->created_at->format('d/m/Y H:i'),
            ],
            'total_paid' => $newTotalPaid,
            'remaining' => max(0, $newRemaining),
            'payment_status' => $order->fresh()->payment_status,
        ]);
    }

    /**
     * Delete a payment record
     */
    public function destroy(Payment $payment)
    {
        $order = $payment->order;
        $amount = $payment->amount;

        ActivityLog::log('deleted', "Pembayaran Rp " . number_format($amount, 0, ',', '.') . " dihapus dari order {$order->order_code}", $order);

        $payment->delete();

        // Recalculate payment status
        $newTotalPaid = $order->payments()->sum('amount');
        if ($newTotalPaid <= 0) {
            $order->update(['payment_status' => 'Belum Bayar']);
        } elseif ($newTotalPaid < $order->grand_total) {
            $order->update(['payment_status' => 'DP']);
        }

        return response()->json([
            'success' => true,
            'message' => 'Pembayaran berhasil dihapus.',
            'total_paid' => $newTotalPaid,
            'remaining' => max(0, $order->grand_total - $newTotalPaid),
            'payment_status' => $order->fresh()->payment_status,
        ]);
    }
}
