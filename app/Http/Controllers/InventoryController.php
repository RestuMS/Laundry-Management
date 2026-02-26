<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $inventories = Inventory::latest()
            ->when($search, function($query, $search) {
                return $query->where('name', 'like', "%{$search}%");
            })
            ->paginate(15)
            ->withQueryString();

        return view('dashboard.inventory', compact('inventories', 'search'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'stock' => 'required|numeric|min:0',
            'unit' => 'required|string|max:20',
            'usage_per_kg' => 'required|numeric|min:0',
            'minimum_stock' => 'required|numeric|min:0',
        ]);

        Inventory::create($request->all());

        return redirect()->back()->with('success', 'Bahan baku berhasil ditambahkan!');
    }

    public function update(Request $request, Inventory $inventory)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'stock' => 'required|numeric|min:0',
            'unit' => 'required|string|max:20',
            'usage_per_kg' => 'required|numeric|min:0',
            'minimum_stock' => 'required|numeric|min:0',
        ]);

        $inventory->update($request->all());

        return redirect()->back()->with('success', 'Bahan baku berhasil diupdate!');
    }

    public function destroy(Inventory $inventory)
    {
        $inventory->delete();
        return redirect()->back()->with('success', 'Bahan baku dihapus.');
    }
}
