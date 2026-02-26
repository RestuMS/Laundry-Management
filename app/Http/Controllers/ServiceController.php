<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Service;

class ServiceController extends Controller
{
    public function index()
    {
        $services = Service::latest()->get();
        return view('dashboard.layanan', compact('services'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'service_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'unit' => 'required|string|max:50',
        ]);

        Service::create($request->all());

        return redirect()->route('layanan.index')->with('success', 'Layanan berhasil ditambahkan!');
    }

    public function update(Request $request, Service $layanan)
    {
        $request->validate([
            'service_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'unit' => 'required|string|max:50',
        ]);

        $layanan->update($request->all());

        return redirect()->route('layanan.index')->with('success', 'Layanan berhasil diperbarui!');
    }

    public function destroy(Service $layanan)
    {
        $layanan->delete();

        return redirect()->route('layanan.index')->with('success', 'Layanan berhasil dihapus!');
    }
}
