@extends('layouts.dashboard')

@section('title', 'Admin Dashboard')

@section('content')

<!-- Content Header Action -->
<div class="flex flex-col sm:flex-row justify-between items-center mb-8 gap-4">
    <!-- Search Bar -->
    <div class="relative w-full max-w-xl glass-card rounded-[18px] focus-within:ring-2 focus-within:ring-primary/40 transition-shadow">
        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
            <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
        </div>
        <input type="text" placeholder="Cari cara borus..." class="block w-full pl-11 pr-4 py-3.5 bg-transparent border-none focus:ring-0 text-[14.5px] text-slate-700 placeholder-slate-400 font-medium" />
    </div>

    <!-- Tambah Order Btn -->
    <button class="flex-shrink-0 bg-primary hover:bg-[#4A7CE0] text-white px-6 py-3.5 rounded-[16px] flex items-center gap-2 font-semibold text-[14.5px] shadow-[0_8px_20px_rgba(91,141,239,0.3)] hover:shadow-[0_10px_25px_rgba(91,141,239,0.4)] transform hover:-translate-y-0.5 transition-all">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
        Tambah Order
    </button>
</div>

<!-- TOP CARDS STATS -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    
    <!-- Card 1: Total Order -->
    <div class="glass-card p-6 rounded-[16px] hover-float relative overflow-hidden group">
        <div class="absolute -right-6 -top-6 w-24 h-24 bg-primary/5 rounded-full blur-xl group-hover:bg-primary/10 transition-colors"></div>
        <div class="flex items-start justify-center mb-4">
            <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-white to-blue-50/50 flex items-center justify-center shadow-sm border border-white/60 p-2">
                <img src="{{ asset('images/icon_1.png') }}" onerror="this.src='{{ asset('images/icon.png') }}'" alt="Icon" class="w-full h-full object-contain drop-shadow-sm">
            </div>
        </div>
        <div class="text-center">
            <h3 class="text-[14px] font-semibold text-slate-500 mb-1">Total Order</h3>
            <p class="text-[28px] font-bold text-slate-700 leading-none tracking-tight">{{ number_format($totalOrder) }}</p>
        </div>
        <div class="mt-3 flex items-center justify-center gap-1.5 text-[12px] font-semibold text-primary">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
            <span>Live Data</span>
        </div>
    </div>

    <!-- Card 2: Order Diproses -->
    <div class="glass-card p-6 rounded-[16px] hover-float relative overflow-hidden group">
        <div class="absolute -right-6 -top-6 w-24 h-24 bg-orange-400/5 rounded-full blur-xl group-hover:bg-orange-400/10 transition-colors"></div>
        <div class="flex items-start justify-center mb-4">
            <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-white to-orange-50/50 flex items-center justify-center shadow-sm border border-white/60 p-2">
                <img src="{{ asset('images/icon_2.png') }}" onerror="this.src='{{ asset('images/icon.png') }}'" alt="Icon" class="w-full h-full object-contain drop-shadow-sm">
            </div>
        </div>
        <div class="text-center">
            <h3 class="text-[14px] font-semibold text-slate-500 mb-1">Order Diproses</h3>
            <p class="text-[28px] font-bold text-slate-700 leading-none tracking-tight">{{ number_format($orderDiproses) }}</p>
        </div>
        <div class="mt-3 flex items-center justify-center gap-1.5 text-[12px] font-semibold text-orange-400">
            <svg class="w-1.5 h-1.5" fill="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="12"></circle></svg>
            <span>Sedang Berjalan</span>
        </div>
    </div>

    <!-- Card 3: Order Selesai -->
    <div class="glass-card p-6 rounded-[16px] hover-float relative overflow-hidden group">
        <div class="absolute -right-6 -top-6 w-24 h-24 bg-green-400/5 rounded-full blur-xl group-hover:bg-green-400/10 transition-colors"></div>
        <div class="flex items-start justify-center mb-4">
            <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-white to-green-50/50 flex items-center justify-center shadow-sm border border-white/60 p-2">
                <img src="{{ asset('images/icon_3.png') }}" onerror="this.src='{{ asset('images/icon.png') }}'" alt="Icon" class="w-full h-full object-contain drop-shadow-sm">
            </div>
        </div>
        <div class="text-center">
            <h3 class="text-[14px] font-semibold text-slate-500 mb-1">Order Selesai</h3>
            <p class="text-[28px] font-bold text-slate-700 leading-none tracking-tight">{{ number_format($orderSelesai) }}</p>
        </div>
        <div class="mt-3 flex items-center justify-center gap-1.5 text-[12px] font-semibold text-green-500">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            <span>Selesai Total</span>
        </div>
    </div>

    <!-- Card 4: Omset Hari Ini -->
    <div class="glass-card p-6 rounded-[16px] hover-float relative overflow-hidden group">
        <div class="absolute -right-6 -top-6 w-24 h-24 bg-green-500/5 rounded-full blur-xl group-hover:bg-green-500/10 transition-colors"></div>
        <div class="flex items-start justify-center mb-4">
            <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-white to-green-50/50 flex items-center justify-center shadow-sm border border-white/60 p-2">
                <img src="{{ asset('images/icon_4.png') }}" onerror="this.src='{{ asset('images/icon.png') }}'" alt="Icon" class="w-full h-full object-contain drop-shadow-sm">
            </div>
        </div>
        <div class="text-center">
            <h3 class="text-[14px] font-semibold text-slate-500 mb-1">Omset Hari Ini</h3>
            <p class="text-[28px] font-bold text-slate-700 leading-none tracking-tight">Rp {{ number_format($omsetHariIni/1000, 0, ',', '.') }}K</p>
        </div>
        <div class="mt-3 flex items-center justify-center gap-1.5 text-[12px] font-semibold text-[#48B868]">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
            <span>Lunas Transaksi</span>
        </div>
    </div>

</div>

<!-- CHARTS ROW -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    
    <!-- Left: Line Chart -->
    <div class="lg:col-span-2 glass-card p-6 rounded-[16px]">
        <div class="flex items-center justify-between mb-6 border-b border-white/60 pb-4">
            <h3 class="text-[16px] font-bold text-slate-700">Grafik Pemasukan Mingguan</h3>
            <select class="text-[13px] font-medium text-slate-500 bg-white/50 border-none rounded-lg px-3 py-1.5 focus:ring-0 cursor-pointer">
                <option>Bulan 20an</option>
                <option>Minggu Ini</option>
            </select>
        </div>
        <div class="w-full h-64 relative">
            <canvas id="revenueChart"></canvas>
        </div>
    </div>

    <!-- Right: Pie Chart -->
    <div class="glass-card p-6 rounded-[16px] flex flex-col">
        <div class="mb-3 border-b border-white/60 pb-4">
            <h3 class="text-[16px] font-bold text-slate-700 text-center">Layanan Terlaris</h3>
        </div>
        <div class="w-full flex-1 min-h-[160px] relative flex items-center justify-center mb-4 mt-2">
            <canvas id="serviceChart"></canvas>
        </div>
        <div class="grid grid-cols-2 gap-y-3 gap-x-2 text-[12.5px] font-semibold text-slate-600 px-2 mt-auto">
            @foreach($topServices as $idx => $svc)
                @php $colors = ['#FDE68A', '#59C98C', '#8FB8FF', '#FF7A7A']; @endphp
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2 truncate pr-2"><span class="w-2.5 h-2.5 rounded shrink-0 bg-[{{ $colors[$idx % 4] }}]"></span> {{ $svc->service_name }}</div>
                    <span>{{ $svc->percentage }}%</span>
                </div>
            @endforeach
        </div>
    </div>

</div>

<!-- BOTTOM DATA ROW -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    
    <!-- Pesanan Terbaru -->
    <div class="lg:col-span-2 glass-card p-6 rounded-[16px]">
        <div class="flex items-center justify-between mb-6 pb-2">
            <h3 class="text-[16px] font-bold text-slate-700">Pesanan Terbaru</h3>
            <button class="text-[13px] font-semibold text-primary hover:text-[#4A7CE0] flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-blue-50/50 hover:bg-blue-100/50 transition-colors border border-blue-100/50">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Lihat Semua
            </button>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="text-[13px] font-semibold text-slate-400 border-b border-white/60">
                        <th class="pb-3 px-2 font-medium">Nama</th>
                        <th class="pb-3 px-2 font-medium">Layanan</th>
                        <th class="pb-3 px-2 font-medium">Status</th>
                        <th class="pb-3 px-2 font-medium text-right">Total</th>
                    </tr>
                </thead>
                <tbody class="text-[14px] font-semibold text-slate-700">
                    @forelse($recentOrders as $ro)
                    <tr class="border-b border-white/60 hover:bg-white/40 transition-colors group">
                        <td class="py-3.5 px-2 flex items-center gap-3">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($ro->customer_name) }}&background=EAF4FF&color=5B8DEF&rounded=true" alt="Avatar" class="w-8 h-8 rounded-full shadow-sm border border-white">
                            {{ $ro->customer_name }}
                        </td>
                        <td class="py-3.5 px-2 text-slate-500 font-medium">
                            {{ $ro->items->count() > 0 ? $ro->items->pluck('service_name')->join(', ') : $ro->service_name }}
                        </td>
                        <td class="py-3.5 px-2">
                            @if($ro->status == 'Selesai' || $ro->status == 'Diambil')
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[12px] font-bold bg-[#59C98C]/10 text-[#59C98C] border border-[#59C98C]/20">
                                Selesai
                            </span>
                            @else
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[12px] font-bold bg-[#FFB84D]/10 text-[#FFB84D] border border-[#FFB84D]/20">
                                Diproses
                            </span>
                            @endif
                        </td>
                        <td class="py-3.5 px-2 text-right">Rp {{ number_format($ro->total_price, 0, ',', '.') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="py-4 text-center text-slate-400">Belum ada pesanan terbaru.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination mockup -->
        <div class="mt-4 flex items-center justify-between text-[12.5px] font-semibold text-slate-400 px-2 pt-2 border-t border-white/20">
            <span>Menampilkan 1-4.053</span>
            <div class="flex gap-2">
                <button class="w-6 h-6 rounded bg-white/60 flex items-center justify-center hover:bg-white transition-colors disable"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg></button>
                <button class="w-6 h-6 rounded bg-primary text-white flex items-center justify-center hover:bg-[#4A7CE0] transition-colors"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg></button>
            </div>
        </div>
    </div>

    <!-- Pelanggan Baru -->
    <div class="glass-card p-6 rounded-[16px]">
        <h3 class="text-[16px] font-bold text-slate-700 mb-6 border-b border-white/60 pb-4 text-center pb-2">Pelanggan Baru</h3>
        
        <div class="space-y-4">
            @forelse($newCustomers as $nc)
            <div class="flex items-center justify-between hover:bg-white/40 p-2 -mx-2 rounded-xl transition-colors cursor-pointer">
                <div class="flex items-center gap-3">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode($nc->full_name) }}&background=random&color=fff&rounded=true" alt="Avatar" class="w-10 h-10 rounded-full shadow-sm border border-white">
                    <span class="font-bold text-[14.5px] text-slate-700">{{ $nc->full_name }}</span>
                </div>
                <div class="flex items-center gap-1 text-[13px] font-semibold text-primary">
                    {{ $nc->status }}
                </div>
            </div>
            @empty
            <div class="text-center text-slate-400 py-4">Belum ada pelanggan.</div>
            @endforelse
        </div>

        <button class="w-full mt-6 flex justify-center py-2.5 rounded-xl border border-primary/20 bg-blue-50/50 hover:bg-primary hover:text-white text-primary text-[13px] font-bold transition-colors">
            + 44 2010
        </button>
    </div>

</div>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        
        // --- Custom Gradient Configs ---
        Chart.defaults.font.family = "'Poppins', sans-serif";
        Chart.defaults.color = '#94a3b8'; // slate-400

        // 1. Line Chart Initialize
        const ctxRev = document.getElementById('revenueChart').getContext('2d');
        
        // Create Gradient for Line Chart
        let gradientLine = ctxRev.createLinearGradient(0, 0, 0, 300);
        gradientLine.addColorStop(0, 'rgba(91, 141, 239, 0.4)');
        gradientLine.addColorStop(1, 'rgba(91, 141, 239, 0.0)');

        new Chart(ctxRev, {
            type: 'line',
            data: {
                labels: {!! $weeklyDates->toJson() !!},
                datasets: [{
                    label: 'Pemasukan (Juta)',
                    data: {!! $weeklyRevenues->toJson() !!},
                    borderColor: '#5B8DEF',
                    backgroundColor: gradientLine,
                    borderWidth: 3,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#5B8DEF',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    fill: true,
                    tension: 0.4 // Soft curve
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#fff',
                        titleColor: '#1e293b',
                        bodyColor: '#5B8DEF',
                        borderColor: '#e2e8f0',
                        borderWidth: 1,
                        padding: 10,
                        boxPadding: 4,
                        callbacks: {
                            label: function(context) {
                                return 'Rp ' + context.parsed.y + ' juta';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        suggestedMin: 0,
                        ticks: {
                            stepSize: 1,
                            callback: function(value) {
                                return 'Rp ' + value + ' juta';
                            },
                            font: { size: 11 }
                        },
                        grid: {
                            color: 'rgba(255, 255, 255, 0.4)',
                            drawBorder: false,
                        }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 11 } }
                    }
                }
            }
        });

            // 2. Doughnut / Pie Chart Initialize
            const ctxSvc = document.getElementById('serviceChart').getContext('2d');
            new Chart(ctxSvc, {
                type: 'doughnut',
                data: {
                    labels: {!! $topServices->pluck('service_name')->toJson() !!},
                    datasets: [{
                        data: {!! $topServices->pluck('percentage')->toJson() !!},
                        backgroundColor: [
                            '#FDE68A', '#59C98C', '#8FB8FF', '#FF7A7A'
                        ],
                        borderWidth: 4,
                        borderColor: '#ffffff',
                        hoverOffset: 4
                    }]
                },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '55%',
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        padding: 10,
                        callbacks: {
                            label: function(context) {
                                return context.label + ': ' + context.parsed + '%';
                            }
                        }
                    }
                }
            }
        });

    });
</script>
@endpush
