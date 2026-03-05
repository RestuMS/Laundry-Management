<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Http\Requests\StoreExpenseRequest;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $month = $request->input('month');
        $date_filter = $request->input('date');
        $search = $request->input('search');

        $query = Expense::query();
        $filterText = '';

        // If specific date is selected
        if ($date_filter) {
            $query->whereDate('date', $date_filter);
            $filterText = \Carbon\Carbon::parse($date_filter)->translatedFormat('d F Y');
            // clear month to avoid confusion in view
            $month = null; 
        } 
        // If month is selected
        elseif ($month) {
            $query->where('date', 'like', $month . '%');
            $filterText = \Carbon\Carbon::parse($month)->translatedFormat('F Y');
        } 
        // Default to current month if nothing is selected
        else {
            $month = Carbon::now()->format('Y-m');
            $query->where('date', 'like', $month . '%');
            $filterText = \Carbon\Carbon::parse($month)->translatedFormat('F Y');
        }

        $expenses = $query->when($search, function($q, $search) {
                return $q->where(function($subQ) use ($search) {
                    $subQ->where('name', 'like', "%{$search}%")
                         ->orWhere('note', 'like', "%{$search}%");
                });
            })
            ->latest('date') // sort by date descending explicitly
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        $totalExpense = $query->sum('amount');

        return view('dashboard.expense', compact('expenses', 'month', 'date_filter', 'search', 'totalExpense', 'filterText'));
    }

    public function store(StoreExpenseRequest $request)
    {
        Expense::create($request->validated());

        return redirect()->back()->with('success', 'Pengeluaran berhasil dicatat!');
    }

    public function destroy(Expense $pengeluaran)
    {
        $pengeluaran->delete();
        return redirect()->back()->with('success', 'Data pengeluaran dihapus.');
    }
}
