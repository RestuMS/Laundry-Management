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
        // Actually, we will just use a modal or simple view for creating, but we can return a view if needed.
        return view('dashboard.order_create', compact('services'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'service_name' => 'required|string|max:255',
            'weight' => 'nullable|numeric|min:0',
            'package_detail' => 'nullable|string|max:255',
            'estimated_finish' => 'nullable|date',
            'total_price' => 'required|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'tax' => 'nullable|numeric|min:0',
            'payment_method' => 'nullable|string|max:255',
            'payment_status' => 'required|in:Belum Bayar,DP,Lunas',
            'notes' => 'nullable|string'
        ]);

        $orderCode = 'ORD-' . strtoupper(uniqid());

        $order = Order::create([
            'order_code' => $orderCode,
            'customer_name' => $request->customer_name,
            'customer_phone' => $request->customer_phone,
            'service_name' => $request->service_name,
            'weight' => $request->weight,
            'package_detail' => $request->package_detail,
            'estimated_finish' => $request->estimated_finish,
            'status' => 'Diterima',
            'total_price' => $request->total_price,
            'discount' => $request->discount ?? 0,
            'tax' => $request->tax ?? 0,
            'payment_method' => $request->payment_method,
            'payment_status' => $request->payment_status,
            'notes' => $request->notes,
        ]);

        app(WhatsappNotificationService::class)->sendTrackingUpdate($order);

        // Deduct inventory items that have a usage logic
        if ($request->weight && $request->weight > 0) {
            $inventories = Inventory::where('usage_per_kg', '>', 0)->get();
            foreach ($inventories as $inv) {
                $inv->stock = max(0, $inv->stock - ($inv->usage_per_kg * $request->weight));
                $inv->save();
            }
        }

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
            'service_name' => 'required|string|max:255',
            'weight' => 'nullable|numeric|min:0',
            'package_detail' => 'nullable|string|max:255',
            'estimated_finish' => 'nullable|date',
            'total_price' => 'required|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'tax' => 'nullable|numeric|min:0',
            'payment_method' => 'nullable|string|max:255',
            'status' => 'required|string',
            'payment_status' => 'required|in:Belum Bayar,DP,Lunas',
            'notes' => 'nullable|string'
        ]);

        $oldStatus = $order->status;
        $order->update($request->all());

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

    public function destroy(Order $order)
    {
        $order->delete();
        return redirect()->route('order.index')->with('success', 'Order telah dihapus.');
    }
}
