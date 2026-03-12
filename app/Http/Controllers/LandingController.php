<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Service;
use App\Models\Setting;
use App\Models\Order;
use App\Models\Customer;
use App\Models\OrderItem;
use App\Models\BookingSlot;

class LandingController extends Controller
{
    public function index()
    {
        $services = Service::whereNull('deleted_at')->orderBy('service_name')->get();
        $settings = Setting::pluck('value', 'key')->toArray();
        
        // Stats for social proof
        $totalCustomers = Customer::count();
        $totalOrders = Order::count();
        
        return view('landing.index', compact('services', 'settings', 'totalCustomers', 'totalOrders'));
    }

    public function submitOrder(Request $request)
    {
        $request->validate([
            'customer_name'         => 'required|string|max:100',
            'customer_phone'        => ['required', 'string', 'max:20', 'regex:/^(\+62|62|08)[0-9]{8,13}$/'],
            'customer_address'      => 'nullable|string|max:255',
            'notes'                 => 'nullable|string|max:500',
            'payment_method'        => 'nullable|string|in:Bayar di Tempat,Transfer',
            'requested_pickup_date' => 'nullable|date|after:today',
            'items'                 => 'required|array|min:1',
            'items.*.service_id'    => 'required|exists:services,id',
            'items.*.quantity'      => 'required|numeric|min:0.1',
        ], [
            'customer_phone.regex'        => 'Format nomor HP tidak valid. Gunakan format 08xx, +62xx, atau 62xx.',
            'requested_pickup_date.after' => 'Tanggal antar harus hari esok atau lebih.',
        ]);

        // Normalize phone number to 08xx format
        $phone = $request->customer_phone;
        $phone = preg_replace('/^\+62/', '0', $phone);
        $phone = preg_replace('/^62/', '0', $phone);

        // Rate Limiting: Max 3 orders per phone number per day
        $todayOrderCount = Order::where('customer_phone', $phone)
            ->whereDate('created_at', today())
            ->count();

        if ($todayOrderCount >= 3) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah mencapai batas maksimal order hari ini (3 order/hari). Silakan coba lagi besok atau hubungi kami langsung.',
            ], 429);
        }

        // Find or create customer
        $customer = Customer::firstOrCreate(
            ['phone' => $phone],
            [
                'full_name' => $request->customer_name,
                'address'   => $request->customer_address ?? '',
            ]
        );

        // Calculate total + build order items
        $totalPrice  = 0;
        $orderItems  = [];
        $totalWeight = 0;
        
        foreach ($request->items as $item) {
            $service  = Service::findOrFail($item['service_id']);
            $subtotal = $service->price * $item['quantity'];
            $totalPrice  += $subtotal;
            $totalWeight += $item['quantity'];
            $orderItems[] = [
                'service_name' => $service->service_name,
                'qty'          => $item['quantity'],
                'unit'         => $service->unit,
                'price'        => $service->price,
                'subtotal'     => $subtotal,
            ];
        }

        // ── Booking Slot Validation ─────────────────────────────
        $bookingSlotId       = null;
        $requestedPickupDate = $request->requested_pickup_date;

        if ($requestedPickupDate) {
            $slot = BookingSlot::reserveCapacity($requestedPickupDate, $totalWeight);

            if ($slot === null) {
                $existingSlot = BookingSlot::getOrCreateForDate($requestedPickupDate);
                if (!$existingSlot->is_open) {
                    return response()->json([
                        'success'    => false,
                        'slot_error' => true,
                        'message'    => 'Maaf, tanggal ' . \Carbon\Carbon::parse($requestedPickupDate)->locale('id')->isoFormat('D MMMM YYYY') . ' sedang ditutup. Silakan pilih tanggal lain.',
                    ], 422);
                }
                return response()->json([
                    'success'    => false,
                    'slot_error' => true,
                    'message'    => 'Maaf, kapasitas tanggal ' . \Carbon\Carbon::parse($requestedPickupDate)->locale('id')->isoFormat('D MMMM YYYY') . ' sudah penuh (' . $existingSlot->booked_kg . '/' . $existingSlot->max_capacity_kg . ' kg). Silakan pilih tanggal lain.',
                ], 422);
            }

            $bookingSlotId = $slot->id;
        }
        // ───────────────────────────────────────────────────────

        // Generate unique order code
        $orderCode = 'ORD-' . strtoupper(substr(md5(uniqid()), 0, 8));

        // Create order with "Menunggu Konfirmasi" status
        $order = Order::create([
            'order_code'            => $orderCode,
            'order_source'          => 'online',
            'booking_slot_id'       => $bookingSlotId,
            'requested_pickup_date' => $requestedPickupDate,
            'customer_id'           => $customer->id,
            'customer_name'         => $request->customer_name,
            'customer_phone'        => $phone,
            'service_name'          => collect($orderItems)->pluck('service_name')->join(', '),
            'weight'                => $totalWeight,
            'total_price'           => $totalPrice,
            'status'                => 'Menunggu Konfirmasi',
            'payment_method'        => $request->input('payment_method', 'Bayar di Tempat'),
            'payment_status'        => 'Belum Bayar',
            'notes'                 => $request->notes,
        ]);

        // Create order items
        foreach ($orderItems as $oi) {
            $oi['order_id'] = $order->id;
            OrderItem::create($oi);
        }

        $successMessage = 'Order berhasil dikirim! Pesanan Anda sedang menunggu konfirmasi dari admin kami.';
        if ($requestedPickupDate) {
            $successMessage .= ' Jadwal antar: ' . \Carbon\Carbon::parse($requestedPickupDate)->locale('id')->isoFormat('dddd, D MMMM YYYY') . '.';
        }
        $successMessage .= ' Simpan kode order untuk tracking.';

        return response()->json([
            'success'     => true,
            'order_code'  => $orderCode,
            'pickup_date' => $requestedPickupDate,
            'message'     => $successMessage,
        ]);
    }
}
