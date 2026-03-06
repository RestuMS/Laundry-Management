<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use App\Models\Order;
use Illuminate\Http\Request;

class ComplaintController extends Controller
{
    public function index()
    {
        $complaints = Complaint::with('order')->latest()->paginate(20);
        return view('dashboard.complaint', compact('complaints'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'order_code' => 'required|string',
            'description' => 'required|string|max:1000',
        ]);

        $order = Order::where('order_code', $request->order_code)->first();

        if (!$order) {
            return response()->json(['success' => false, 'message' => 'Pesanan tidak ditemukan.'], 404);
        }

        Complaint::create([
            'order_id' => $order->id,
            'description' => $request->description,
            'status' => 'Menunggu',
        ]);

        return response()->json(['success' => true, 'message' => 'Keluhan Anda berhasil dikirim dan akan segera diproses oleh tim kami.']);
    }

    public function updateStatus(Request $request, Complaint $complaint)
    {
        $request->validate([
            'status' => 'required|in:Menunggu,Direview,Disetujui,Ditolak,Diganti Uang',
            'resolution_notes' => 'nullable|string|max:1000'
        ]);

        $complaint->update([
            'status' => $request->status,
            'resolution_notes' => $request->resolution_notes
        ]);

        return redirect()->back()->with('success', 'Status tiket keluhan berhasil diperbarui.');
    }

    public function destroy(Complaint $complaint)
    {
        $complaint->delete();
        return redirect()->back()->with('success', 'Tiket keluhan berhasil dihapus.');
    }
}
