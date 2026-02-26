@extends('layouts.dashboard')

@section('title', 'Owner Executive Dashboard')
@section('header_title', 'Executive Overview')

@section('content')

<!-- Custom CSS Animations for Owner UI -->
<style>
/* Premium Soft Glass */
.premium-glass {
    background: rgba(255, 255, 255, 0.45);
    backdrop-filter: blur(24px);
    -webkit-backdrop-filter: blur(24px);
    border: 1px solid rgba(255, 255, 255, 0.8);
    box-shadow: 0 10px 40px -10px rgba(91, 141, 239, 0.15);
}

/* Hover Floating Premium Effect */
.premium-hover {
    transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}
.premium-hover:hover {
    transform: translateY(-5px) scale(1.01);
    box-shadow: 0 15px 50px -10px rgba(91, 141, 239, 0.25);
    background: rgba(255, 255, 255, 0.65);
}

/* Skeleton Shimmer */
.skeleton {
    background: #e2e8f0;
    background-image: linear-gradient(90deg, rgba(255,255,255,0) 0, rgba(255,255,255,0.4) 20%, rgba(255,255,255,0) 40%, rgba(255,255,255,0));
    background-size: 200px 100%;
    background-repeat: no-repeat;
    animation: shimmer 1.5s infinite linear;
}
@keyframes shimmer {
    0% { background-position: -200px 0; }
    100% { background-position: calc(200px + 100%) 0; }
}
</style>

<!-- Date Range Picker & Export -->
<div class="flex flex-col sm:flex-row justify-between items-center mb-8 gap-4" x-data="{ loading: true }" x-init="setTimeout(() => loading = false, 500)">
    <div>
        <p class="text-[13px] font-bold text-slate-400 uppercase tracking-widest mb-1">Analitik Bisnis</p>
        <h2 class="text-3xl font-bold text-slate-800 tracking-tight">Performa Cabang</h2>
    </div>

    <template x-if="!loading">
        <div class="flex items-center gap-3">
            <button class="px-5 py-2.5 rounded-xl text-[13px] font-bold bg-white text-slate-600 border border-white hover:bg-white/80 transition-all shadow-sm focus:outline-none flex items-center gap-2 premium-glass">
                <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                Oktober 2026
            </button>
            <button class="px-5 py-2.5 rounded-xl text-[13px] font-bold bg-primary text-white hover:bg-[#4A7CE0] transition-all shadow-[0_8px_20px_rgba(91,141,239,0.3)] hover:shadow-[0_12px_25px_rgba(91,141,239,0.4)] focus:outline-none flex items-center gap-2 transform hover:-translate-y-0.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                Export Laporan
            </button>
        </div>
    </template>
</div>

<!-- EXECUTIVE STATS (Large Cards) -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8" x-data="{ loading: true }" x-init="setTimeout(() => loading = false, 800)">
    
    <!-- Mega Card: Total Omset -->
    <div style="height: 140px;">
        <template x-if="loading"><div class="h-full w-full rounded-2xl skeleton"></div></template>
        <template x-if="!loading">
            <div class="h-full premium-glass p-6 rounded-2xl premium-hover relative overflow-hidden flex flex-col justify-center">
                <div class="absolute -right-8 -bottom-8 w-32 h-32 bg-primary/10 rounded-full blur-2xl"></div>
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-[14px] font-bold text-slate-500 uppercase tracking-wide">Total Omset Bulan Ini</h3>
                    <div class="p-2 bg-primary/10 rounded-lg text-primary">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>
                <div class="flex items-end gap-3 z-10">
                    <span class="text-[32px] font-black text-slate-800 tracking-tight leading-none">Rp {{ number_format($omsetBulanIni / 1000000, 1, ',', '') }} <span class="text-xl text-slate-500 font-bold">Juta</span></span>
                </div>
                <div class="mt-2 text-[13px] font-bold {{ $omsetGrowth >= 0 ? 'text-emerald-500' : 'text-red-500' }} flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="{{ $omsetGrowth >= 0 ? 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6' : 'M13 17h8m0 0V9m0 8l-8-8-4 4-6-6' }}"></path></svg>
                    {{ $omsetGrowth >= 0 ? '+' : '' }}{{ $omsetGrowth }}% dari bulan lalu
                </div>
            </div>
        </template>
    </div>

    <!-- Mega Card: Pertumbuhan Pelanggan -->
    <div style="height: 140px;">
        <template x-if="loading"><div class="h-full w-full rounded-2xl skeleton"></div></template>
        <template x-if="!loading">
            <div class="h-full premium-glass p-6 rounded-2xl premium-hover relative overflow-hidden flex flex-col justify-center">
                <div class="absolute -right-8 -bottom-8 w-32 h-32 bg-emerald-400/10 rounded-full blur-2xl"></div>
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-[14px] font-bold text-slate-500 uppercase tracking-wide">Pelanggan Aktif</h3>
                    <div class="p-2 bg-emerald-500/10 rounded-lg text-emerald-500">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    </div>
                </div>
                <div class="flex items-end gap-3 z-10">
                    <span class="text-[32px] font-black text-slate-800 tracking-tight leading-none">{{ number_format($totalPelanggan, 0, ',', '.') }}</span>
                </div>
                <div class="mt-2 text-[13px] font-bold text-emerald-500 flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                    +{{ $pelangganBaruBulanIni }} pelanggan bulan ini
                </div>
            </div>
        </template>
    </div>

    <!-- Mega Card: Order Hari Ini -->
    <div style="height: 140px;">
        <template x-if="loading"><div class="h-full w-full rounded-2xl skeleton"></div></template>
        <template x-if="!loading">
            <div class="h-full premium-glass p-6 rounded-2xl premium-hover relative overflow-hidden flex flex-col justify-center">
                <div class="absolute -right-8 -bottom-8 w-32 h-32 bg-orange-400/10 rounded-full blur-2xl"></div>
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-[14px] font-bold text-slate-500 uppercase tracking-wide">Total Order Hari Ini</h3>
                    <div class="p-2 bg-orange-500/10 rounded-lg text-orange-500">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                    </div>
                </div>
                <div class="flex items-end gap-3 z-10">
                    <span class="text-[32px] font-black text-slate-800 tracking-tight leading-none">{{ $totalOrderHariIni }}</span>
                    <span class="text-[14px] font-bold text-slate-400 mb-1">Tiket</span>
                </div>
                <div class="mt-2 text-[13px] font-bold text-orange-400 flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="{{ $diffOrderHarian >= 0 ? 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6' : 'M13 17h8m0 0V9m0 8l-8-8-4 4-6-6' }}"></path></svg>
                    {{ $diffOrderHarian >= 0 ? '+' : '' }}{{ $diffOrderHarian }} order dari rata-rata
                </div>
            </div>
        </template>
    </div>

</div>

<!-- AREA CHART & RANKINGS ROW -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8" x-data="{ loading: true }" x-init="setTimeout(() => loading = false, 1100)">
    
    <!-- Mega Area Chart (2 Cols) -->
    <div class="lg:col-span-2 premium-glass p-6 rounded-[24px]">
        <template x-if="loading"><div class="h-full w-full rounded-xl skeleton min-h-[300px]"></div></template>
        <template x-if="!loading">
            <div class="h-full flex flex-col">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="text-[18px] font-bold text-slate-800">Performa Pencapaian Cabang Utama</h3>
                        <p class="text-[13px] text-slate-500 font-medium">Berdasarkan Total Pendapatan Kotor (Rupiah)</p>
                    </div>
                    <div class="flex items-center bg-white/50 rounded-lg p-1 border border-white">
                        <button class="px-3 py-1.5 text-[12px] font-bold bg-white text-primary rounded-md shadow-sm">Daily</button>
                        <button class="px-3 py-1.5 text-[12px] font-bold text-slate-500 hover:text-primary transition-colors">Weekly</button>
                    </div>
                </div>
                <div class="w-full h-72 relative flex-1">
                    <canvas id="areaBranchChart"></canvas>
                </div>
            </div>
        </template>
    </div>

    <!-- Ranking Layanan Terlaris (1 Col) -->
    <div class="premium-glass p-0 rounded-[24px] overflow-hidden flex flex-col">
        <template x-if="loading"><div class="h-full w-full rounded-xl skeleton min-h-[300px]"></div></template>
        <template x-if="!loading">
            <div class="flex flex-col h-full">
                <div class="p-6 border-b border-white/60 bg-white/30 backdrop-blur-md">
                    <h3 class="text-[18px] font-bold text-slate-800">Layanan Terlaris Premium</h3>
                    <p class="text-[13px] text-slate-500 font-medium">Top 4 layanan kontributor omset</p>
                </div>
                
                <div class="p-6 flex-1 space-y-5">
                    
                    @forelse($topServices as $idx => $ts)
                    @php
                        $colorsId = $idx % 5;
                        $styles = [
                            ['labelBg' => 'bg-[#FFD700]/20 text-[#FFD700]', 'barBg' => 'from-primary to-[#7FB3FF]'],
                            ['labelBg' => 'bg-slate-300/40 text-slate-500', 'barBg' => 'from-emerald-400 to-emerald-300'],
                            ['labelBg' => 'bg-amber-600/20 text-amber-700', 'barBg' => 'from-orange-400 to-orange-300'],
                            ['labelBg' => 'bg-slate-200/50 text-slate-500', 'barBg' => 'from-purple-400 to-purple-300'],
                            ['labelBg' => 'bg-blue-200/50 text-blue-500', 'barBg' => 'from-rose-400 to-rose-300']
                        ];
                        $style = $styles[$colorsId];
                    @endphp
                    <div>
                        <div class="flex justify-between items-center mb-1.5">
                            <span class="text-[14px] font-bold text-slate-700 flex items-center gap-2"><span class="w-5 h-5 rounded-md {{ $style['labelBg'] }} flex items-center justify-center text-[11px]">{{ $idx+1 }}</span> {{ $ts->service_name }}</span>
                            <span class="text-[14px] font-bold text-slate-800">Rp {{ number_format($ts->total_revenue/1000000, 1, ',', '') }} Jt</span>
                        </div>
                        <div class="w-full bg-slate-200/50 rounded-full h-2.5 overflow-hidden border border-white/50">
                            <div class="bg-gradient-to-r {{ $style['barBg'] }} h-2.5 rounded-full" style="width: {{ $ts->percentage }}%"></div>
                        </div>
                    </div>
                    @empty
                    <div class="text-[13px] text-slate-400 font-bold p-4">Belum ada layanan selesai.</div>
                    @endforelse

                </div>
            </div>
        </template>
    </div>

</div>

<!-- BOTTOM DATA ROW: Top Customers Loyal -->
<div class="premium-glass p-0 rounded-[24px] overflow-hidden" x-data="{ loading: true }" x-init="setTimeout(() => loading = false, 1400)">
    <template x-if="loading"><div class="w-full h-[300px] skeleton"></div></template>
    <template x-if="!loading">
        <div class="flex flex-col">
            <div class="p-6 border-b border-white/60 bg-white/30 backdrop-blur-md flex justify-between items-center">
                <div>
                    <h3 class="text-[18px] font-bold text-slate-800">Ranking Pelanggan Loyal (Sultan)</h3>
                    <p class="text-[13px] text-slate-500 font-medium">Berdasarkan frekuensi & volume transaksi terbesar</p>
                </div>
                <button class="text-[13px] font-bold text-primary hover:underline hover:text-[#4A7CE0] transition-colors">Lihat Semua Pelanggan</button>
            </div>
            
            <div class="p-2">
                <table class="w-full text-left whitespace-nowrap">
                    <tbody>
                        @forelse($topCustomers as $idx => $tc)
                        @php
                            $colorsId = $idx % 3;
                            $styles = [
                                'bg-gradient-to-br from-yellow-300 to-yellow-500',
                                'bg-gradient-to-br from-slate-300 to-slate-400',
                                'bg-gradient-to-br from-amber-600 to-amber-700'
                            ];
                            $style = $styles[$colorsId] ?? $styles[0];
                        @endphp
                        <tr class="hover:bg-white/50 transition-colors group rounded-xl">
                            <td class="py-4 px-4 pl-6 w-16">
                                <div class="w-8 h-8 rounded-full {{ $style }} text-white flex items-center justify-center font-black text-[14px] shadow-sm">{{ $idx+1 }}</div>
                            </td>
                            <td class="py-4 px-4 min-w-[200px]">
                                <div class="flex items-center gap-4">
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($tc->customer_name) }}&background=random&rounded=true&bold=true" alt="Avatar" class="w-10 h-10 rounded-full shadow-sm ring-2 ring-white">
                                    <div>
                                        <div class="font-bold text-slate-800 text-[15px]">{{ $tc->customer_name }}</div>
                                        <div class="text-[12px] font-semibold text-primary mt-0.5">{{ $tc->customer_phone ?? 'Tanpa Nomor' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 px-4 text-center">
                                <span class="text-[13px] font-bold text-slate-500">{{ $tc->total_trx }}x Transaksi</span>
                            </td>
                            <td class="py-4 px-4 pr-6 text-right">
                                <div class="font-black text-[16px] text-slate-800">Rp {{ number_format($tc->total_spend, 0, ',', '.') }}</div>
                                <div class="text-[12px] text-slate-400 font-medium">Total Spend</div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-[13px] text-slate-400 font-bold p-10 text-center">Belum ada data pelanggan yang menyelesaikan pesanan (Lunas).</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </template>
</div>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Delay Chart Initialization slightly after Skeleton finishes
        setTimeout(() => {
            const ctxArea = document.getElementById('areaBranchChart');
            if(ctxArea) {
                // Smooth Gradient Area for Chart.js
                const gradientArea1 = ctxArea.getContext('2d').createLinearGradient(0, 0, 0, 300);
                gradientArea1.addColorStop(0, 'rgba(91, 141, 239, 0.6)'); // primary
                gradientArea1.addColorStop(1, 'rgba(91, 141, 239, 0.05)');

                const gradientArea2 = ctxArea.getContext('2d').createLinearGradient(0, 0, 0, 300);
                gradientArea2.addColorStop(0, 'rgba(89, 201, 140, 0.5)'); // emerald
                gradientArea2.addColorStop(1, 'rgba(89, 201, 140, 0.05)');

                // Custom Tooltips configuration logic specific to SaaS UI
                Chart.defaults.font.family = "'Poppins', sans-serif";

                new Chart(ctxArea, {
                    type: 'line',
                    data: {
                        labels: {!! $weeklyDates->toJson() !!},
                        datasets: [
                            {
                                label: 'Cabang Pusat (Kinerja Harian)',
                                data: {!! $weeklyRevenues->toJson() !!},
                                borderColor: '#5B8DEF',
                                backgroundColor: gradientArea1,
                                borderWidth: 3,
                                pointBackgroundColor: '#fff',
                                pointBorderColor: '#5B8DEF',
                                pointBorderWidth: 2,
                                pointRadius: 4,
                                pointHoverRadius: 6,
                                fill: true,
                                tension: 0.4 // Smooth curve
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: {
                            mode: 'index',
                            intersect: false,
                        },
                        plugins: {
                            legend: { 
                                display: true,
                                position: 'top',
                                align: 'end',
                                labels: {
                                    usePointStyle: true,
                                    boxWidth: 8,
                                    color: '#64748b',
                                    font: { size: 12, weight: 'bold' }
                                }
                            },
                            tooltip: {
                                backgroundColor: 'rgba(255, 255, 255, 0.95)',
                                titleColor: '#1e293b',
                                bodyColor: '#475569',
                                borderColor: '#e2e8f0',
                                borderWidth: 1,
                                padding: 12,
                                boxPadding: 6,
                                usePointStyle: true,
                                callbacks: {
                                    label: function(context) {
                                        return context.dataset.label + ': Rp ' + context.parsed.y + ' jt';
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                title: { display: true, text: 'Omset (Juta Rupiah)', color: '#94a3b8', font: {size: 11, weight: 'bold'}},
                                ticks: {
                                    color: '#94a3b8',
                                    font: { size: 11, weight: '600' }
                                },
                                grid: {
                                    color: 'rgba(255, 255, 255, 0.4)',
                                    drawBorder: false,
                                }
                            },
                            x: {
                                grid: { display: false },
                                ticks: { 
                                    color: '#64748b',
                                    font: { size: 11, weight: '600' } 
                                }
                            }
                        }
                    }
                });
            }
        }, 1200);
    });
</script>
@endpush
