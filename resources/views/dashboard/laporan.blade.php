@extends('layouts.dashboard')

@section('title', 'Laporan Keuangan')
@section('header_title', 'Laporan & Analitik')

@section('content')

<!-- Custom Styles for Reports -->
<style>
/* Glassmorphism Containers */
.glass-panel {
    background: rgba(255, 255, 255, 0.75);
    backdrop-filter: blur(16px);
    -webkit-backdrop-filter: blur(16px);
    border: 1px solid rgba(255, 255, 255, 0.9);
}

.report-card {
    transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    background: linear-gradient(145deg, rgba(255,255,255,0.9) 0%, rgba(255,255,255,0.6) 100%);
}
.report-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 15px 35px -5px rgba(91, 141, 239, 0.2);
}

/* Button Gradients */
.btn-excel {
    background: linear-gradient(135deg, #20D071 0%, #16A355 100%);
    box-shadow: 0 4px 15px rgba(32, 208, 113, 0.3);
}
.btn-pdf {
    background: linear-gradient(135deg, #FF6B6B 0%, #E03E3E 100%);
    box-shadow: 0 4px 15px rgba(255, 107, 107, 0.3);
}
.btn-filter {
    transition: all 0.2s ease;
}
.btn-filter:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(91, 141, 239, 0.25);
}

/* Icon box soft colors */
.icon-box-blue { background: #EAF4FF; color: #5B8DEF; }
.icon-box-violet { background: #F3E8FF; color: #9333EA; }
.icon-box-emerald { background: #E5F7EA; color: #48B868; }

</style>

<div x-data="{ loading: true, filterDate: 'This Month' }" x-init="setTimeout(() => loading = false, 800)">

    <!-- Top Action Bar: Filters & Exports -->
    <div class="flex flex-col xl:flex-row justify-between items-start xl:items-center mb-8 gap-5">
        
        <!-- Date Filters -->
        <div class="flex items-center gap-3 bg-white/50 backdrop-blur-md p-1.5 rounded-2xl border border-white shadow-sm">
            <button @click="filterDate = 'Today'" :class="filterDate === 'Today' ? 'bg-white shadow-md text-primary font-bold' : 'text-slate-500 hover:text-slate-700 font-medium'" class="px-5 py-2 rounded-xl text-[13.5px] transition-all">Hari Ini</button>
            <button @click="filterDate = 'This Week'" :class="filterDate === 'This Week' ? 'bg-white shadow-md text-primary font-bold' : 'text-slate-500 hover:text-slate-700 font-medium'" class="px-5 py-2 rounded-xl text-[13.5px] transition-all">Minggu Ini</button>
            <button @click="filterDate = 'This Month'" :class="filterDate === 'This Month' ? 'bg-white shadow-md text-primary font-bold' : 'text-slate-500 hover:text-slate-700 font-medium'" class="px-5 py-2 rounded-xl text-[13.5px] transition-all">Bulan Ini</button>
            
            <!-- Custom Date Range -->
            <div class="h-6 w-px bg-slate-300 mx-1"></div>
            <button class="px-4 py-2 text-slate-500 hover:text-primary transition-colors flex items-center gap-2 text-[13.5px] font-bold">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                Custom
            </button>
        </div>

        <!-- Export Buttons -->
        <div class="flex gap-4 w-full xl:w-auto">
            <a href="{{ route('laporan.export.excel', ['month' => request('month', \Carbon\Carbon::now()->format('Y-m'))]) }}" class="flex-1 xl:flex-none btn-excel text-white px-6 py-2.5 rounded-2xl flex items-center justify-center gap-2.5 font-bold text-[14px] btn-filter group">
                <svg class="w-5 h-5 group-hover:-translate-y-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                Export Excel
            </a>
            <a href="{{ route('laporan.export.pdf', ['month' => request('month', \Carbon\Carbon::now()->format('Y-m'))]) }}" target="_blank" class="flex-1 xl:flex-none btn-pdf text-white px-6 py-2.5 rounded-2xl flex items-center justify-center gap-2.5 font-bold text-[14px] btn-filter group">
                <svg class="w-5 h-5 group-hover:-translate-y-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                Export PDF
            </a>
        </div>

    </div>

    <!-- Top Analytics Cards (Harian, Mingguan, Bulanan) -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 lg:gap-6 mb-8">
        
        <!-- Harian -->
        <div class="glass-panel report-card p-5 xl:p-6 rounded-[24px]">
            <div class="flex justify-between items-start mb-4">
                <div class="w-12 h-12 rounded-2xl icon-box-blue flex items-center justify-center shadow-inner">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <div class="bg-[#E5F7EA] text-[#48B868] text-[12px] font-bold px-2.5 py-1 rounded-lg flex items-center gap-1">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 10l7-7m0 0l7 7m-7-7v18"></path></svg>
                    2.4%
                </div>
            </div>
            <h4 class="text-[14px] font-bold text-slate-500 mb-1 uppercase tracking-wide">Omset Hari Ini</h4>
            <div class="text-[28px] font-black text-slate-800">Rp {{ number_format($omsetHariIni, 0, ',', '.') }}</div>
            <div class="text-[13px] text-slate-400 font-medium mt-2">Dari transaksi lunas</div>
        </div>

        <!-- Bulanan -->
        <div class="glass-panel report-card p-5 xl:p-6 rounded-[24px]">
            <div class="flex justify-between items-start mb-4">
                <div class="w-12 h-12 rounded-2xl icon-box-violet flex items-center justify-center shadow-inner">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                </div>
                <div class="bg-[#E5F7EA] text-[#48B868] text-[12px] font-bold px-2.5 py-1 rounded-lg flex items-center gap-1">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 10l7-7m0 0l7 7m-7-7v18"></path></svg>
                    14.5%
                </div>
            </div>
            <h4 class="text-[14px] font-bold text-slate-500 mb-1 uppercase tracking-wide">Omset Pemasukan (Bulan Ini)</h4>
            <div class="text-[26px] font-black text-[#5B8DEF]">Rp {{ number_format($omsetBulanIni, 0, ',', '.') }}</div>
            <div class="text-[13px] text-slate-400 font-medium mt-2">Kotor Belum Dipotong</div>
        </div>

        <!-- Pengeluaran -->
        <div class="glass-panel report-card p-5 xl:p-6 rounded-[24px]">
            <div class="flex justify-between items-start mb-4">
                <div class="w-12 h-12 rounded-2xl bg-red-50 text-red-500 flex items-center justify-center shadow-inner">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path></svg>
                </div>
            </div>
            <h4 class="text-[14px] font-bold text-slate-500 mb-1 uppercase tracking-wide">Pengeluaran (Bulan Ini)</h4>
            <div class="text-[26px] font-black text-slate-800">Rp {{ number_format($pengeluaranBulanIni, 0, ',', '.') }}</div>
            <div class="text-[13px] text-slate-400 font-medium mt-2">Operasional / Bahan</div>
        </div>

        <!-- Laba Bersih -->
        <div class="glass-panel report-card p-5 xl:p-6 rounded-[24px] relative overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-[#1E6DEB]/5 to-transparent"></div>
            <div class="relative z-10">
                <div class="flex justify-between items-start mb-4">
                    <div class="w-12 h-12 rounded-2xl icon-box-emerald flex items-center justify-center shadow-inner">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>
                <h4 class="text-[14px] font-bold text-slate-500 mb-1 uppercase tracking-wide">Laba Bersih (Bulan Ini)</h4>
                <div class="text-[26px] font-black text-[#20D071]">Rp {{ number_format($labaBersihBulanIni, 0, ',', '.') }}</div>
                <div class="text-[13px] text-slate-400 font-medium mt-2">Omset dikurangi Pengeluaran</div>
            </div>
        </div>

    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 mb-8 min-h-[400px]">
        
        <!-- Line Chart: Trend Pemasukan -->
        <div class="glass-panel p-6 rounded-[24px] xl:col-span-2 relative flex flex-col">
            
            <div x-show="loading" class="absolute inset-0 z-10 bg-white/50 backdrop-blur-sm rounded-[24px] flex items-center justify-center">
                <div class="w-10 h-10 border-4 border-[#5B8DEF] border-t-transparent rounded-full animate-spin"></div>
            </div>
            
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h3 class="text-[18px] font-bold text-slate-800">Trend Pemasukan Bulanan</h3>
                    <p class="text-[13px] text-slate-500 font-medium mt-1">Grafik pertumbuhan omset kotor seluruh interaksi.</p>
                </div>
                <button class="w-8 h-8 rounded-full bg-white border border-slate-200 text-slate-400 hover:text-primary flex items-center justify-center shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path></svg>
                </button>
            </div>
            
            <div class="flex-1 w-full relative min-h-[320px]">
                <canvas id="revenueLineChart" style="position: absolute; width: 100%; height: 100%;"></canvas>
            </div>
        </div>

        <!-- Bar Chart: Top Services Output -->
        <div class="glass-panel p-6 rounded-[24px] relative flex flex-col">
            
            <div x-show="loading" class="absolute inset-0 z-10 bg-white/50 backdrop-blur-sm rounded-[24px] flex items-center justify-center">
                <div class="w-10 h-10 border-4 border-[#5B8DEF] border-t-transparent rounded-full animate-spin"></div>
            </div>
            
            <div class="mb-6">
                <h3 class="text-[18px] font-bold text-slate-800">Distribusi Layanan</h3>
                <p class="text-[13px] text-slate-500 font-medium mt-1">Estimasi kontribusi per produk.</p>
            </div>
            
            <div class="flex-1 w-full relative min-h-[320px]">
                <canvas id="serviceBarChart" style="position: absolute; width: 100%; height: 100%;"></canvas>
            </div>
            
        </div>

    </div>

</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        
        // Setup Chart defaults for popping UI
        Chart.defaults.font.family = "'Poppins', sans-serif";
        Chart.defaults.color = '#94A3B8';
        Chart.defaults.scale.grid.color = 'rgba(255, 255, 255, 0.4)';
        Chart.defaults.scale.grid.tickColor = 'transparent';

        setTimeout(() => {
            /* 1. Revenue Line Chart (Area Chart Style) */
            const ctxLine = document.getElementById('revenueLineChart').getContext('2d');
            
            // Create Gradient for Line Chart
            let gradientBlue = ctxLine.createLinearGradient(0, 0, 0, 300);
            gradientBlue.addColorStop(0, 'rgba(91, 141, 239, 0.5)');   // Primary blue soft
            gradientBlue.addColorStop(1, 'rgba(91, 141, 239, 0.0)');

            new Chart(ctxLine, {
                type: 'line',
                data: {
                    labels: {!! $chartLineLabels->toJson() !!},
                    datasets: [{
                        label: 'Omset',
                        data: {!! $chartLineData->toJson() !!},
                        borderColor: '#5B8DEF',
                        backgroundColor: gradientBlue,
                        borderWidth: 4,
                        pointBackgroundColor: '#fff',
                        pointBorderColor: '#5B8DEF',
                        pointBorderWidth: 3,
                        pointRadius: 5,
                        pointHoverRadius: 7,
                        fill: true,
                        tension: 0.4 // Smooth curve
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: 'rgba(255, 255, 255, 0.95)',
                            titleColor: '#1E293B',
                            bodyColor: '#475569',
                            borderColor: 'rgba(91, 141, 239, 0.2)',
                            borderWidth: 1,
                            padding: 12,
                            boxPadding: 6,
                            usePointStyle: true,
                            bodyFont: { font: { weight: 'bold' } },
                            callbacks: {
                                label: function(context) {
                                    let value = context.raw;
                                    return ' Rp ' + value + ' Ribu';
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            suggestedMin: 0,
                            suggestedMax: Math.max(...{!! $chartLineData->toJson() !!}.length > 0 ? {!! $chartLineData->toJson() !!} : [0]) * 1.5 || 10,
                            border: { display: false },
                            ticks: {
                                maxTicksLimit: 6,
                                callback: function(value) {
                                    return value / 1000 + 'Jt';
                                },
                                padding: 10,
                                font: { weight: '600' }
                            }
                        },
                        x: {
                            border: { display: false },
                            grid: { display: false },
                            ticks: { font: { weight: '600' }, maxRotation: 0, autoSkip: true }
                        }
                    },
                    interaction: {
                        intersect: false,
                        mode: 'index',
                    },
                }
            });

            /* 2. Service Distribution Bar Chart */
            const ctxBar = document.getElementById('serviceBarChart').getContext('2d');
            
            // Bar Gradients
            let barBlue = ctxBar.createLinearGradient(0, 0, 0, 300);
            barBlue.addColorStop(0, '#5B8DEF');
            barBlue.addColorStop(1, '#8FB8FF');
            
            let barOrange = ctxBar.createLinearGradient(0, 0, 0, 300);
            barOrange.addColorStop(0, '#FFB84D');
            barOrange.addColorStop(1, '#FFD185');

            let barGreen = ctxBar.createLinearGradient(0, 0, 0, 300);
            barGreen.addColorStop(0, '#59C98C');
            barGreen.addColorStop(1, '#81D8A9');

            new Chart(ctxBar, {
                type: 'bar',
                data: {
                    labels: {!! $chartBarLabels->toJson() !!},
                    datasets: [{
                        label: 'Penjualan',
                        data: {!! $chartBarData->toJson() !!},
                        backgroundColor: [barBlue, barOrange, barGreen, '#DBEAFE', '#FDE68A'],
                        borderRadius: 8,
                        borderSkipped: false,
                        barThickness: 28
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: 'rgba(255, 255, 255, 0.95)',
                            titleColor: '#1E293B',
                            bodyColor: '#475569',
                            borderColor: 'rgba(91, 141, 239, 0.2)',
                            borderWidth: 1,
                            padding: 12,
                            cornerRadius: 8,
                            displayColors: false,
                            callbacks: {
                                label: function(context) {
                                    return context.raw + '% Kontribusi';
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            display: false, // hide vertical axis
                            beginAtZero: true
                        },
                        x: {
                            border: { display: false },
                            grid: { display: false },
                            ticks: { font: { weight: '600', size: 11 }, maxRotation: 0, autoSkip: true }
                        }
                    }
                }
            });
            
        }, 850); // delay to let alpine skeleton finish

    });
</script>
@endpush
@endsection
