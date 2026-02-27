<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Order;

use App\Models\Service;
use App\Models\Customer;
use App\Models\Inventory;
use App\Services\WhatsappNotificationService;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $orders = Order::latest()
            ->when($search, function ($query, $search) {
                return $query->where('customer_name', 'like', "%{$search}%")
                             ->orWhere('order_code', 'like', "%{$search}%")
                             ->orWhere('customer_phone', 'like', "%{$search}%");
            })
            ->paginate(10)
            ->withQueryString();

        return view('dashboard.order', compact('orders', 'search'));
    }

    public function create()
    {
        $services = Service::all();
        
        // Fetch unique customer names and phones for autocomplete suggestions
        $customers = Order::select('customer_name', 'customer_phone')
            ->distinct()
            ->orderBy('customer_name')
            ->get();
            
        // Actually, we will just use a modal or simple view for creating, but we can return a view if needed.
        return view('dashboard.order_create', compact('services', 'customers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'estimated_finish' => 'nullable|date',
            'total_price' => 'required|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'tax' => 'nullable|numeric|min:0',
            'payment_method' => 'nullable|string|max:255',
            'payment_status' => 'required|in:Belum Bayar,DP,Lunas',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.service_name' => 'required|string|max:255',
            'items.*.qty' => 'required|numeric|min:0.01',
            'items.*.unit' => 'required|string',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.subtotal' => 'required|numeric|min:0',
        ]);

        $orderCode = 'ORD-' . strtoupper(uniqid());

        $order = Order::create([
            'order_code' => $orderCode,
            'customer_name' => $request->customer_name,
            'customer_phone' => $request->customer_phone,
            'estimated_finish' => $request->estimated_finish,
            'status' => 'Diterima',
            'total_price' => $request->total_price,
            'discount' => $request->discount ?? 0,
            'tax' => $request->tax ?? 0,
            'payment_method' => $request->payment_method,
            'payment_status' => $request->payment_status,
            'notes' => $request->notes,
        ]);

        foreach ($request->items as $item) {
            $order->items()->create([
                'service_name' => $item['service_name'],
                'qty' => $item['qty'],
                'unit' => $item['unit'],
                'price' => $item['price'],
                'subtotal' => $item['subtotal'],
            ]);
            
            // Deduct inventory dynamically based on qty if logic needs it, basic usage rule per qty
            $inventories = Inventory::where('usage_per_kg', '>', 0)->get();
            foreach ($inventories as $inv) {
                $inv->stock = max(0, $inv->stock - ($inv->usage_per_kg * $item['qty']));
                $inv->save();
            }
        }

        app(WhatsappNotificationService::class)->sendTrackingUpdate($order);

        return redirect()->route('order.index')->with('success', 'Order berhasil ditambahkan! Notifikasi WhatsApp otomatis dikirim.');
    }

    public function edit(Order $order)
    {
        $services = Service::all();
        return view('dashboard.order_edit', compact('order', 'services'));
    }

    public function update(Request $request, Order $order)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'estimated_finish' => 'nullable|date',
            'total_price' => 'required|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'tax' => 'nullable|numeric|min:0',
            'payment_method' => 'nullable|string|max:255',
            'status' => 'required|string',
            'payment_status' => 'required|in:Belum Bayar,DP,Lunas',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.service_name' => 'required|string|max:255',
            'items.*.qty' => 'required|numeric|min:0.01',
            'items.*.unit' => 'required|string',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.subtotal' => 'required|numeric|min:0',
        ]);

        $oldStatus = $order->status;
        $order->update($request->except('items'));
        
        $order->items()->delete(); // Recreate items for simplicity on update
        foreach ($request->items as $item) {
            $order->items()->create([
                'service_name' => $item['service_name'],
                'qty' => $item['qty'],
                'unit' => $item['unit'],
                'price' => $item['price'],
                'subtotal' => $item['subtotal'],
            ]);
        }

        if ($oldStatus !== $order->status) {
            app(WhatsappNotificationService::class)->sendTrackingUpdate($order);
        }

        return redirect()->route('order.index')->with('success', 'Order berhasil diupdate! Notifikasi WhatsApp otomatis dikirim jika status berubah.');
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|string'
        ]);

        $oldStatus = $order->status;
        $order->update(['status' => $request->status]);

        if ($oldStatus !== $order->status) {
            app(WhatsappNotificationService::class)->sendTrackingUpdate($order);
        }

        return response()->json(['success' => true, 'message' => 'Status berhasil diupdate!', 'new_status' => $order->status]);
    }

    public function updatePaymentStatus(Request $request, Order $order)
    {
        $request->validate([
            'payment_status' => 'required|in:Belum Bayar,Belum Lunas,DP,Lunas,Lunas Cetak'
        ]);

        $order->update(['payment_status' => $request->payment_status]);

        return response()->json(['success' => true, 'message' => 'Status Pembayaran berhasil diupdate!', 'new_status' => $order->payment_status]);
    }

    public function invoice(Order $order)
    {
        return view('dashboard.order_invoice', compact('order'));
    }

    public function sendInvoiceWa(Order $order)
    {
        $sent = app(WhatsappNotificationService::class)->sendInvoice($order);

        if ($sent) {
            return redirect()->back()->with('success', 'Berhasil! Tagihan Invoice WhatsApp telah dikirim ke nomor pelanggan melalui Gateway.');
        } else {
            return redirect()->back()->with('error', 'Gagal kirim WA! Pastikan nomor pelanggan valid atau FONNTE_TOKEN sudah terpasang di .env/pengaturan.');
        }
    }

    public function scanPickup(Request $request)
    {
        $request->validate([
            'order_code' => 'required|string'
        ]);

        $order = Order::where('order_code', $request->order_code)->first();

        if (!$order) {
            return response()->json(['success' => false, 'message' => 'Pesanan tidak ditemukan di database.']);
        }

        if ($order->status == 'Diambil') {
            return response()->json(['success' => false, 'message' => 'Pesanan ini sudah pernah Diambil sebelumnya.']);
        }

        $oldStatus = $order->status;
        $order->update([
            'status' => 'Diambil',
            'payment_status' => 'Lunas' // Auto-lunas if taken
        ]);

        if ($oldStatus !== 'Diambil') {
            app(WhatsappNotificationService::class)->sendTrackingUpdate($order);
        }

        return response()->json([
            'success' => true, 
            'message' => 'Berhasil! Pesanan ' . $order->customer_name . ' berhasil diselesaikan.',
            'order' => $order
        ]);
    }

    public function destroy(Order $order)
    {
        $order->delete();
        return redirect()->route('order.index')->with('success', 'Order telah dihapus.');
    }
}
