<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Order;
use App\Models\OrderPhoto;
use App\Models\Service;
use App\Models\Customer;
use App\Models\Inventory;
use App\Services\WhatsappNotificationService;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderRequest;
use Illuminate\Support\Facades\Storage;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $orders = Order::with(['items', 'photos'])->latest()
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
        
        // Fetch customers from customers table for autocomplete suggestions
        $customers = Customer::select('id', 'full_name', 'phone')
            ->orderBy('full_name')
            ->get();
            
        return view('dashboard.order_create', compact('services', 'customers'));
    }

    public function store(StoreOrderRequest $request)
    {
        $validated = $request->validated();

        $orderCode = 'ORD-' . strtoupper(uniqid());

        // Find or create Customer record
        $customerId = $validated['customer_id'] ?? null;
        
        if (!$customerId && !empty($validated['customer_name'])) {
            // Try to find existing customer by name + phone
            $customer = Customer::where('full_name', $validated['customer_name'])
                ->when($validated['customer_phone'] ?? null, function ($q, $phone) {
                    return $q->where('phone', $phone);
                })
                ->first();

            if (!$customer) {
                // Create new customer automatically
                $customer = Customer::create([
                    'full_name' => $validated['customer_name'],
                    'phone' => $validated['customer_phone'] ?? null,
                    'status' => 'Reguler',
                ]);
            }

            $customerId = $customer->id;
        }

        // Observer will handle: recording status history + sending WA notification
        $order = Order::create([
            'order_code' => $orderCode,
            'customer_id' => $customerId,
            'customer_name' => $validated['customer_name'],
            'customer_phone' => $validated['customer_phone'] ?? null,
            'estimated_finish' => $validated['estimated_finish'] ?? null,
            'status' => 'Diterima',
            'total_price' => $validated['total_price'],
            'discount' => $validated['discount'] ?? 0,
            'tax' => $validated['tax'] ?? 0,
            'payment_method' => $validated['payment_method'] ?? null,
            'payment_status' => $validated['payment_status'],
            'notes' => $validated['notes'] ?? null,
        ]);

        // Increment customer total_orders
        if ($customerId) {
            Customer::where('id', $customerId)->increment('total_orders');
        }

        foreach ($validated['items'] as $item) {
            $order->items()->create([
                'service_name' => $item['service_name'],
                'qty' => $item['qty'],
                'unit' => $item['unit'],
                'price' => $item['price'],
                'subtotal' => $item['subtotal'],
            ]);
            
            // Deduct inventory dynamically based on qty
            $inventories = Inventory::where('usage_per_kg', '>', 0)->get();
            foreach ($inventories as $inv) {
                $inv->stock = max(0, $inv->stock - ($inv->usage_per_kg * $item['qty']));
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

    public function update(UpdateOrderRequest $request, Order $order)
    {
        $validated = $request->validated();

        // 1. Mengembalikan stok inventory dari order item lama (sebelum dihapus)
        foreach ($order->items as $oldItem) {
            $inventories = Inventory::where('usage_per_kg', '>', 0)->get();
            foreach ($inventories as $inv) {
                $inv->stock = $inv->stock + ($inv->usage_per_kg * $oldItem->qty);
                $inv->save();
            }
        }

        $order->update(collect($validated)->except('items')->toArray());
        
        $order->items()->delete();
        
        // 2. Memotong stok inventory sesuai order item baru sekaligus menyimpan item baru
        foreach ($validated['items'] as $item) {
            $order->items()->create([
                'service_name' => $item['service_name'],
                'qty' => $item['qty'],
                'unit' => $item['unit'],
                'price' => $item['price'],
                'subtotal' => $item['subtotal'],
            ]);

            // Deduct inventory dynamically based on qty baru
            $inventories = Inventory::where('usage_per_kg', '>', 0)->get();
            foreach ($inventories as $inv) {
                // Minimum stock is 0 (tidak minus)
                $inv->stock = max(0, $inv->stock - ($inv->usage_per_kg * $item['qty']));
                $inv->save();
            }
        }

        $statusChanged = $order->wasChanged('status');
        $msg = 'Order berhasil diupdate!';
        if ($statusChanged) {
            $msg .= ' Notifikasi WhatsApp otomatis dikirim karena status berubah.';
        }

        return redirect()->route('order.index')->with('success', $msg);
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|string'
        ]);

        // Observer automatically handles WA notification + status history recording
        $order->update(['status' => $request->status]);

        return response()->json([
            'success' => true, 
            'message' => 'Status berhasil diupdate! Notifikasi WA otomatis terkirim.', 
            'new_status' => $order->status
        ]);
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

        // Observer handles WA notification + history recording automatically
        $order->update([
            'status' => 'Diambil',
            'payment_status' => 'Lunas'
        ]);

        return response()->json([
            'success' => true, 
            'message' => 'Berhasil! Pesanan ' . $order->customer_name . ' berhasil diselesaikan. Notifikasi WA otomatis terkirim.',
            'order' => $order
        ]);
    }

    /**
     * Confirm a pending online order (Menunggu Konfirmasi → Diterima)
     */
    public function confirmOrder(Order $order)
    {
        if ($order->status !== 'Menunggu Konfirmasi') {
            return response()->json(['success' => false, 'message' => 'Order ini sudah dikonfirmasi sebelumnya.']);
        }

        $order->update(['status' => 'Diterima']);

        return response()->json([
            'success' => true,
            'message' => 'Order ' . $order->order_code . ' berhasil dikonfirmasi! Notifikasi WA otomatis dikirim ke pelanggan.',
        ]);
    }

    /**
     * Reject a pending online order
     */
    public function rejectOrder(Request $request, Order $order)
    {
        if ($order->status !== 'Menunggu Konfirmasi') {
            return response()->json(['success' => false, 'message' => 'Order ini sudah dikonfirmasi/ditolak.']);
        }

        $reason = $request->input('reason', 'Tidak ada alasan');

        $order->update([
            'status' => 'Ditolak',
            'notes'  => ($order->notes ? $order->notes . ' | ' : '') . 'Ditolak: ' . $reason,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Order ' . $order->order_code . ' ditolak. Notifikasi dikirim ke pelanggan.',
        ]);
    }

    /**
     * Get photos for an order (JSON)
     */
    public function getPhotos(Order $order)
    {
        $photos = $order->photos()->orderBy('type')->latest()->get()->map(fn($p) => [
            'id' => $p->id,
            'url' => $p->photo_url,
            'caption' => $p->caption,
            'type' => $p->type,
            'type_label' => $p->type_label,
            'uploaded_by' => $p->uploaded_by,
            'created_at' => $p->created_at->format('d M Y, H:i'),
        ]);

        return response()->json([
            'success' => true,
            'photos' => $photos,
        ]);
    }

    /**
     * Upload photos for an order
     */
    public function uploadPhotos(Request $request, Order $order)
    {
        $request->validate([
            'photos' => 'required|array|min:1|max:5',
            'photos.*' => 'required|image|mimes:jpeg,jpg,png,webp|max:5120',
            'photo_type' => 'required|in:masuk,proses,selesai',
            'caption' => 'nullable|string|max:255',
        ], [
            'photos.*.max' => 'Ukuran foto maksimal 5MB per file.',
            'photos.*.image' => 'File harus berupa gambar.',
            'photos.max' => 'Maksimal 5 foto per upload.',
        ]);

        $uploaded = 0;

        foreach ($request->file('photos') as $photo) {
            $path = $photo->store('order-photos/' . $order->id, 'public');

            OrderPhoto::create([
                'order_id' => $order->id,
                'photo_path' => $path,
                'caption' => $request->caption,
                'type' => $request->photo_type,
                'uploaded_by' => auth()->user()->name ?? 'System',
            ]);

            $uploaded++;
        }

        return response()->json([
            'success' => true,
            'message' => $uploaded . ' foto berhasil diupload.',
            'photos' => $order->photos()->latest()->take($uploaded)->get()->map(fn($p) => [
                'id' => $p->id,
                'url' => $p->photo_url,
                'caption' => $p->caption,
                'type' => $p->type,
                'type_label' => $p->type_label,
                'uploaded_by' => $p->uploaded_by,
                'created_at' => $p->created_at->format('d M Y, H:i'),
            ]),
        ]);
    }

    /**
     * Delete a photo
     */
    public function deletePhoto(OrderPhoto $photo)
    {
        Storage::disk('public')->delete($photo->photo_path);
        $photo->delete();

        return response()->json([
            'success' => true,
            'message' => 'Foto berhasil dihapus.',
        ]);
    }

    public function destroy(Order $order)
    {
        $order->delete();
        return redirect()->route('order.index')->with('success', 'Order telah dihapus.');
    }
}
