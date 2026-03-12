<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use Illuminate\Http\Request;
use App\Http\Requests\StoreInventoryRequest;
use App\Http\Requests\UpdateInventoryRequest;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $month = $request->input('month');

        $inventories = Inventory::latest()
            ->when($search, function($query, $search) {
                return $query->where('name', 'like', "%{$search}%");
            })
            ->when($month, function($query, $month) {
                $year = substr($month, 0, 4);
                $m = substr($month, 5, 2);
                return $query->whereYear('updated_at', $year)->whereMonth('updated_at', $m);
            })
            ->paginate(15)
            ->withQueryString();

        return view('dashboard.inventory', compact('inventories', 'search', 'month'));
    }

    public function store(StoreInventoryRequest $request)
    {
        $inventory = Inventory::create($request->validated());

        if ($inventory->stock > 0) {
            \App\Models\InventoryLog::create([
                'inventory_id' => $inventory->id,
                'type' => 'in',
                'qty' => $inventory->stock,
                'notes' => 'Stok awal',
                'user_name' => auth()->user()->name ?? 'System',
            ]);
        }

        return redirect()->back()->with('success', 'Bahan baku berhasil ditambahkan!');
    }

    public function adjustStock(Request $request, Inventory $inventory)
    {
        $request->validate([
            'type' => 'required|in:in,out',
            'qty' => 'required|numeric|min:0.01',
            'notes' => 'nullable|string|max:255'
        ]);

        $qty = $request->qty;
        
        if ($request->type === 'in') {
            $inventory->stock += $qty;
        } else {
            // Pastikan stok tidak minus
            if ($inventory->stock < $qty) {
                return redirect()->back()->with('error', 'Stok tidak cukup untuk dikurangi!');
            }
            $inventory->stock -= $qty;
        }

        $inventory->save();

        \App\Models\InventoryLog::create([
            'inventory_id' => $inventory->id,
            'type' => $request->type,
            'qty' => $qty,
            'notes' => $request->notes ?? 'Penyesuaian manual',
            'user_name' => auth()->user()->name ?? 'System',
        ]);

        return redirect()->back()->with('success', 'Stok bahan baku berhasil disesuaikan!');
    }

    public function update(UpdateInventoryRequest $request, Inventory $inventory)
    {
        $inventory->update($request->validated());

        return redirect()->back()->with('success', 'Bahan baku berhasil diupdate!');
    }

    public function destroy(Inventory $inventory)
    {
        $inventory->delete();
        return redirect()->back()->with('success', 'Bahan baku dihapus.');
    }
}
