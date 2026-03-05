<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Expense;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $inputDate = $request->input('date');
        $baseDate = $inputDate ? Carbon::parse($inputDate)->endOfDay() : Carbon::now()->endOfDay();
        
        $startOfMonth = $baseDate->copy()->startOfMonth();
        $endOfMonth = $baseDate->copy()->endOfMonth();
        $startOfWeek = $baseDate->copy()->startOfWeek();
        
        $isCurrentMonth = Carbon::now()->format('Y-m') === $baseDate->format('Y-m');

        $omsetHariIni = Order::whereDate('created_at', Carbon::today())->where('payment_status', 'Lunas')->sum('total_price');
        $omsetMingguIni = Order::whereBetween('created_at', [$startOfWeek, $baseDate])->where('payment_status', 'Lunas')->sum('total_price');
        
        // Compute for the specific Date Input
        $omsetBulanIni = Order::whereDate('created_at', $baseDate)->where('payment_status', 'Lunas')->sum('total_price');
        $pengeluaranBulanIni = Expense::whereDate('date', $baseDate->format('Y-m-d'))->sum('amount');
        $labaBersihBulanIni = $omsetBulanIni - $pengeluaranBulanIni;

        // Line Chart (Daily revenue for the last 7 days from the base date)
        $chartLineLabels = collect();
        $chartLineData = collect();
        for ($i = 6; $i >= 0; $i--) {
            $date = $baseDate->copy()->subDays($i);
            $chartLineLabels->push($date->locale('id')->shortDayName . ' ' . $date->format('d'));
            $sum = Order::whereDate('created_at', $date)->where('payment_status', 'Lunas')->sum('total_price');
            $chartLineData->push($sum / 1000); // in thousands
        }

        // Bar Chart (Top Services for the selected date)
        $topServices = tap(OrderItem::whereHas('order', function($q) use ($baseDate) {
                $q->whereDate('created_at', $baseDate);
            })
            ->selectRaw('service_name, count(*) as total')
            ->groupBy('service_name')
            ->orderByDesc('total')
            ->take(5)
            ->get(), function($list) {
                $total = $list->sum('total') ?: 1;
                $list->each(function($item) use ($total) {
                    $item->percentage = round(($item->total / $total) * 100);
                });
            });

        $chartBarLabels = $topServices->pluck('service_name');
        $chartBarData = $topServices->pluck('percentage');
        
        $filterText = $baseDate->translatedFormat('d M Y');

        return view('dashboard.laporan', compact(
            'omsetHariIni', 'omsetMingguIni', 'omsetBulanIni',
            'pengeluaranBulanIni', 'labaBersihBulanIni',
            'chartLineLabels', 'chartLineData',
            'chartBarLabels', 'chartBarData',
            'filterText', 'isCurrentMonth',
            'inputDate'
        ));
    }

    public function exportPdf(Request $request)
    {
        $date = $request->input('date');
        $baseDate = $date ? Carbon::parse($date) : Carbon::now();
        $startOfMonth = $baseDate->copy()->startOfMonth();
        $endOfMonth = $baseDate->copy()->endOfMonth();

        $orders = Order::whereDate('created_at', $baseDate)
                       ->where('payment_status', 'Lunas')
                       ->orderBy('created_at')
                       ->get();
                       
        $expenses = Expense::whereDate('date', $baseDate->format('Y-m-d'))
                           ->orderBy('date')
                           ->get();

        $omset = $orders->sum('total_price');
        $pengeluaran = $expenses->sum('amount');
        $laba = $omset - $pengeluaran;

        $month = $baseDate->format('Y-m');
        return view('dashboard.laporan_print', compact('month', 'orders', 'expenses', 'omset', 'pengeluaran', 'laba'));
    }

    public function exportExcel(Request $request)
    {
        $date = $request->input('date');
        $baseDate = $date ? Carbon::parse($date) : Carbon::now();
        $startOfMonth = $baseDate->copy()->startOfMonth();
        $endOfMonth = $baseDate->copy()->endOfMonth();

        $orders = Order::whereDate('created_at', $baseDate)
                       ->where('payment_status', 'Lunas')
                       ->orderBy('created_at')
                       ->get();
                       
        $expenses = Expense::whereDate('date', $baseDate->format('Y-m-d'))
                           ->orderBy('date')
                           ->get();

        $fileName = "Laporan_Keuangan_".$baseDate->format('d_M_Y').".csv";
        
        $headers = array(
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        );

        $columns = array('Tipe Transaksi', 'Tanggal', 'Keterangan', 'Pemasukan (Rp)', 'Pengeluaran (Rp)');

        $callback = function() use($orders, $expenses, $columns) {
            $file = fopen('php://output', 'w');
            
            // Add UTF-8 BOM for proper Excel display
            fputs($file, "\xEF\xBB\xBF");
            fputcsv($file, $columns, ';'); // Use semicolon for European/Indonesian excel compatibility
            
            $totalPemasukan = 0;
            $totalPengeluaran = 0;

            foreach ($orders as $order) {
                fputcsv($file, array(
                    'Pemasukan Order',
                    Carbon::parse($order->created_at)->format('Y-m-d H:i'),
                    $order->items->pluck('service_name')->join(', ') . ' (' . $order->customer_name . ')',
                    $order->total_price,
                    0
                ), ';');
                $totalPemasukan += $order->total_price;
            }

            foreach ($expenses as $expense) {
                fputcsv($file, array(
                    'Pengeluaran Kas',
                    $expense->date,
                    $expense->name . ($expense->note ? ' - ' . $expense->note : ''),
                    0,
                    $expense->amount
                ), ';');
                $totalPengeluaran += $expense->amount;
            }
            
            fputcsv($file, array('', '', '', '', ''), ';');
            fputcsv($file, array('', '', 'TOTAL TRANSAKSI', $totalPemasukan, $totalPengeluaran), ';');
            fputcsv($file, array('', '', 'LABA BERSIH', $totalPemasukan - $totalPengeluaran, ''), ';');

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
