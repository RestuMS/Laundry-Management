<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Customer;
use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;

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

    public function store(StoreCustomerRequest $request)
    {
        Customer::create($request->validated());

        return redirect()->route('pelanggan.index')->with('success', 'Pelanggan berhasil ditambahkan!');
    }

    public function update(UpdateCustomerRequest $request, Customer $pelanggan)
    {
        $pelanggan->update($request->validated());

        return redirect()->route('pelanggan.index')->with('success', 'Data Pelanggan berhasil diperbarui!');
    }

    public function destroy(Customer $pelanggan)
    {
        $pelanggan->delete();
        return redirect()->route('pelanggan.index')->with('success', 'Pelanggan berhasil dihapus!');
    }
}
