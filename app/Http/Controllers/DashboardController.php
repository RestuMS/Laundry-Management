<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Order;
use App\Models\Customer;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

        // Top Cards
        $totalOrder = Order::count();
        $orderDiproses = Order::whereNotIn('status', ['Selesai', 'Diambil'])->count();
        $orderSelesai = Order::where('status', 'Selesai')->count();
        $omsetHariIni = Order::whereDate('created_at', $today)
                             ->where('payment_status', 'Lunas')
                             ->sum('total_price');

        // Recent Orders
        $recentOrders = Order::latest()->take(4)->get();

        // New Customers
        $newCustomers = Customer::latest()->take(3)->get();

        // Charts Data: Weekly Revenue (Last 7 Days)
        $weeklyDates = collect();
        $weeklyRevenues = collect();
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $weeklyDates->push($date->format('D'));
            $sum = Order::whereDate('created_at', $date)
                        ->where('payment_status', 'Lunas')
                        ->sum('total_price');
            $weeklyRevenues->push(round($sum / 1000000, 2)); // in Millions for chart
        }

        // Charts Data: Top Services (Pie)
        $topServices = tap(Order::selectRaw('service_name, count(*) as total')
                            ->groupBy('service_name')
                            ->orderByDesc('total')
                            ->take(4)
                            ->get(), function($list) {
                                // Calculate percentages
                                $total = $list->sum('total') ?: 1;
                                $list->each(function($item) use ($total) {
                                    $item->percentage = round(($item->total / $total) * 100);
                                });
                            });

        return view('dashboard.index', compact(
            'totalOrder', 
            'orderDiproses', 
            'orderSelesai', 
            'omsetHariIni',
            'recentOrders',
            'newCustomers',
            'weeklyDates',
            'weeklyRevenues',
            'topServices'
        ));
    }

    public function kasir()
    {
        $today = Carbon::today();
        
        $totalOrderHariIni = Order::whereDate('created_at', $today)->count();
        $orders = Order::latest()->paginate(10); // Recent orders for table
        
        return view('dashboard.kasir', compact('totalOrderHariIni', 'orders'));
    }

    public function owner()
    {
        $today = Carbon::today();
        $startOfMonth = Carbon::now()->startOfMonth();
        $lastMonthStart = Carbon::now()->subMonth()->startOfMonth();
        $lastMonthEnd = Carbon::now()->subMonth()->endOfMonth();

        // 1. Total Omset Bulan Ini (Hanya Lunas)
        $omsetBulanIni = Order::whereBetween('created_at', [$startOfMonth, Carbon::now()])
                              ->where('payment_status', 'Lunas')
                              ->sum('total_price');

        $omsetBulanLalu = Order::whereBetween('created_at', [$lastMonthStart, $lastMonthEnd])
                               ->where('payment_status', 'Lunas')
                               ->sum('total_price');

        // Kalkulasi Persentase Pertumbuhan
        $omsetGrowth = 0;
        if ($omsetBulanLalu > 0) {
            $omsetGrowth = round((($omsetBulanIni - $omsetBulanLalu) / $omsetBulanLalu) * 100, 1);
        } elseif ($omsetBulanIni > 0) {
            $omsetGrowth = 100;
        }

        // 2. Pelanggan Aktif
        $totalPelanggan = Customer::count();
        $pelangganBaruBulanIni = Customer::whereBetween('created_at', [$startOfMonth, Carbon::now()])->count();

        // 3. Total Order Hari Ini
        $totalOrderHariIni = Order::whereDate('created_at', $today)->count();
        $firstOrderDate = Order::min('created_at');
        $daysSinceFirstOrder = max(1, $firstOrderDate ? Carbon::now()->diffInDays($firstOrderDate) + 1 : 1);
        // avoid divide by zero if DB is empty sometimes diffInDays is weird, max(1, ...)
        $rataRataOrderHarian = Order::count() / $daysSinceFirstOrder;
        $diffOrderHarian = $totalOrderHariIni - round($rataRataOrderHarian);

        // 4. Ranking Layanan Terlaris (Revenue)
        $topServices = tap(Order::selectRaw('service_name, sum(total_price) as total_revenue')
            ->where('payment_status', 'Lunas')
            ->groupBy('service_name')
            ->orderByDesc('total_revenue')
            ->take(4)
            ->get(), function($list) {
                $totalAll = $list->sum('total_revenue') ?: 1;
                $list->each(function($item) use ($totalAll) {
                    $item->percentage = round(($item->total_revenue / $totalAll) * 100);
                });
            });

        // 5. Ranking Pelanggan Loyal
        $topCustomers = Order::selectRaw('customer_name, customer_phone, count(*) as total_trx, sum(total_price) as total_spend')
            ->where('payment_status', 'Lunas')
            ->groupBy('customer_name', 'customer_phone')
            ->orderByDesc('total_spend')
            ->take(3)
            ->get();

        // 6. Chart Area: Performa Pencapaian
        $weeklyDates = collect();
        $weeklyRevenues = collect();
        
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $weeklyDates->push($date->format('d M'));
            // Daily mock for Branch 1 just mapped to real data
            $sum = Order::whereDate('created_at', $date)
                        ->where('payment_status', 'Lunas')
                        ->sum('total_price');
            $weeklyRevenues->push(round($sum / 1000000, 2)); // in millions
        }

        return view('dashboard.owner', compact(
            'omsetBulanIni',
            'omsetGrowth',
            'totalPelanggan',
            'pelangganBaruBulanIni',
            'totalOrderHariIni',
            'diffOrderHarian',
            'topServices',
            'topCustomers',
            'weeklyDates',
            'weeklyRevenues'
        ));
    }
}
