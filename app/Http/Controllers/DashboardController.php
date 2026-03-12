<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Customer;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
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
        $topServices = tap(OrderItem::selectRaw('service_name, count(*) as total')
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

    public function kasir(Request $request)
    {
        $today = Carbon::today();
        $search = $request->input('search');
        
        $totalOrderHariIni = Order::whereDate('created_at', $today)->count();
        $orders = Order::latest()
            ->when($search, function ($query, $search) {
                return $query->where(function($q) use ($search) {
                    $q->where('customer_name', 'like', "%{$search}%")
                      ->orWhere('order_code', 'like', "%{$search}%")
                      ->orWhere('customer_phone', 'like', "%{$search}%");
                });
            })
            ->paginate(10)
            ->withQueryString();
        
        return view('dashboard.kasir', compact('totalOrderHariIni', 'orders', 'search'));
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
        $rataRataOrderHarian = Order::count() / $daysSinceFirstOrder;
        $diffOrderHarian = $totalOrderHariIni - round($rataRataOrderHarian);

        // 4. Ranking Layanan Terlaris
        $topServices = tap(OrderItem::selectRaw('order_items.service_name, sum(order_items.subtotal) as total_revenue, count(*) as total_orders')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.payment_status', 'Lunas')
            ->groupBy('order_items.service_name')
            ->orderByDesc('total_revenue')
            ->take(5)
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
            ->take(5)
            ->get();

        // 6. Grafik Revenue 12 Bulan (Monthly)
        $monthlyLabels = collect();
        $monthlyRevenues = collect();
        $monthlyOrders = collect();
        for ($i = 11; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $monthlyLabels->push($month->locale('id')->isoFormat('MMM YY'));
            $revenue = Order::whereYear('created_at', $month->year)
                            ->whereMonth('created_at', $month->month)
                            ->where('payment_status', 'Lunas')
                            ->sum('total_price');
            $monthlyRevenues->push(round($revenue / 1000, 0)); // in thousands
            $monthlyOrders->push(
                Order::whereYear('created_at', $month->year)
                     ->whereMonth('created_at', $month->month)
                     ->count()
            );
        }

        // 7. Grafik Harian 7 Hari (Weekly)
        $weeklyDates = collect();
        $weeklyRevenues = collect();
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $weeklyDates->push($date->format('d M'));
            $sum = Order::whereDate('created_at', $date)
                        ->where('payment_status', 'Lunas')
                        ->sum('total_price');
            $weeklyRevenues->push(round($sum / 1000000, 2));
        }

        // 8. Rating Statistics
        $avgRating = \App\Models\OrderRating::avg('rating') ?? 0;
        $totalRatings = \App\Models\OrderRating::count();
        $ratingDistribution = \App\Models\OrderRating::selectRaw('rating, count(*) as total')
            ->groupBy('rating')
            ->orderByDesc('rating')
            ->get()
            ->keyBy('rating');

        // 9. Ulasan Terbaru
        $recentReviews = \App\Models\OrderRating::with('order')
            ->latest()
            ->take(5)
            ->get();

        // 10. Total Order Selesai & Pending
        $totalSelesai = Order::where('status', 'Diambil')->count();
        $totalPending = Order::whereNotIn('status', ['Selesai', 'Diambil'])->count();

        return view('dashboard.owner', compact(
            'omsetBulanIni', 'omsetGrowth',
            'totalPelanggan', 'pelangganBaruBulanIni',
            'totalOrderHariIni', 'diffOrderHarian',
            'topServices', 'topCustomers',
            'weeklyDates', 'weeklyRevenues',
            'monthlyLabels', 'monthlyRevenues', 'monthlyOrders',
            'avgRating', 'totalRatings', 'ratingDistribution', 'recentReviews',
            'totalSelesai', 'totalPending'
        ));
    }
}
