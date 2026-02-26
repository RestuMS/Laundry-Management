<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Order;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        $startOfWeek = Carbon::now()->startOfWeek();
        $startOfMonth = Carbon::now()->startOfMonth();

        $omsetHariIni = Order::whereDate('created_at', $today)->where('payment_status', 'Lunas')->sum('total_price');
        $omsetMingguIni = Order::whereBetween('created_at', [$startOfWeek, Carbon::now()])->where('payment_status', 'Lunas')->sum('total_price');
        $omsetBulanIni = Order::whereBetween('created_at', [$startOfMonth, Carbon::now()])->where('payment_status', 'Lunas')->sum('total_price');

        // Line Chart (Daily revenue for this week)
        $chartLineLabels = collect();
        $chartLineData = collect();
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $chartLineLabels->push($date->locale('id')->shortDayName);
            $sum = Order::whereDate('created_at', $date)->where('payment_status', 'Lunas')->sum('total_price');
            $chartLineData->push($sum / 1000); // in thousands
        }

        // Bar Chart (Top Services)
        $topServices = tap(Order::selectRaw('service_name, count(*) as total')
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

        return view('dashboard.laporan', compact(
            'omsetHariIni', 'omsetMingguIni', 'omsetBulanIni',
            'chartLineLabels', 'chartLineData',
            'chartBarLabels', 'chartBarData'
        ));
    }
}
