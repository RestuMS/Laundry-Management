<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Customer;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        
        $customers = Customer::when($search, function($query) use ($search) {
                        return $query->where('full_name', 'like', "%{$search}%")
                                     ->orWhere('phone', 'like', "%{$search}%");
                     })->latest()->paginate(10)->withQueryString();

        return view('dashboard.pelanggan', compact('customers', 'search'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'status' => 'required|string',
            'notes' => 'nullable|string'
        ]);

        Customer::create($request->all());

        return redirect()->route('pelanggan.index')->with('success', 'Pelanggan berhasil ditambahkan!');
    }

    public function update(Request $request, Customer $pelanggan)
    {
        $request->validate([
            'full_name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'status' => 'required|string',
            'notes' => 'nullable|string'
        ]);

        $pelanggan->update($request->all());

        return redirect()->route('pelanggan.index')->with('success', 'Data Pelanggan berhasil diperbarui!');
    }

    public function destroy(Customer $pelanggan)
    {
        $pelanggan->delete();
        return redirect()->route('pelanggan.index')->with('success', 'Pelanggan berhasil dihapus!');
    }
}
