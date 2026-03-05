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
        Inventory::create($request->validated());

        return redirect()->back()->with('success', 'Bahan baku berhasil ditambahkan!');
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
