<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Service;
use App\Models\Setting;
use App\Models\Order;
use App\Models\Customer;
use App\Models\OrderItem;

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
            'customer_name'  => 'required|string|max:100',
            'customer_phone' => ['required', 'string', 'max:20', 'regex:/^(\+62|62|08)[0-9]{8,13}$/'],
            'customer_address' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:500',
            'payment_method' => 'nullable|string|in:Bayar di Tempat,Transfer',
            'items' => 'required|array|min:1',
            'items.*.service_id' => 'required|exists:services,id',
            'items.*.quantity' => 'required|numeric|min:0.1',
        ], [
            'customer_phone.regex' => 'Format nomor HP tidak valid. Gunakan format 08xx, +62xx, atau 62xx.',
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
                'address' => $request->customer_address ?? '',
            ]
        );

        // Calculate total
        $totalPrice = 0;
        $orderItems = [];
        
        foreach ($request->items as $item) {
            $service = Service::findOrFail($item['service_id']);
            $subtotal = $service->price * $item['quantity'];
            $totalPrice += $subtotal;
            $orderItems[] = [
                'service_name' => $service->service_name,
                'qty'          => $item['quantity'],
                'unit'         => $service->unit,
                'price'        => $service->price,
                'subtotal'     => $subtotal,
            ];
        }

        // Generate unique order code
        $orderCode = 'ORD-' . strtoupper(substr(md5(uniqid()), 0, 8));

        // Create order with "Menunggu Konfirmasi" status
        $order = Order::create([
            'order_code'     => $orderCode,
            'order_source'   => 'online',
            'customer_id'    => $customer->id,
            'customer_name'  => $request->customer_name,
            'customer_phone' => $phone,
            'service_name'   => collect($orderItems)->pluck('service_name')->join(', '),
            'weight'         => collect($orderItems)->sum('qty'),
            'total_price'    => $totalPrice,
            'status'         => 'Menunggu Konfirmasi',
            'payment_method' => $request->input('payment_method', 'Bayar di Tempat'),
            'payment_status' => 'Belum Bayar',
            'notes'          => $request->notes,
        ]);

        // Create order items
        foreach ($orderItems as $oi) {
            $oi['order_id'] = $order->id;
            OrderItem::create($oi);
        }

        return response()->json([
            'success' => true,
            'order_code' => $orderCode,
            'message' => 'Order berhasil dikirim! Pesanan Anda sedang menunggu konfirmasi dari admin kami. Simpan kode order untuk tracking.',
        ]);
    }
}
