@extends('layouts.dashboard')

@section('title', 'Owner Executive Dashboard')
@section('header_title', 'Executive Overview')

@section('content')

<style>
.premium-glass {
    background: rgba(255, 255, 255, 0.45);
    backdrop-filter: blur(24px);
    -webkit-backdrop-filter: blur(24px);
    border: 1px solid rgba(255, 255, 255, 0.8);
    box-shadow: 0 10px 40px -10px rgba(91, 141, 239, 0.15);
}
.premium-hover {
    transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}
.premium-hover:hover {
    transform: translateY(-4px) scale(1.01);
    box-shadow: 0 15px 50px -10px rgba(91, 141, 239, 0.25);
    background: rgba(255, 255, 255, 0.65);
}
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
.star-filled { color: #FBBF24; }
.star-empty { color: #E2E8F0; }
</style>

<div x-data="{ chartMode: 'monthly' }">

{{-- ===== HEADER ===== --}}
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4">
    <div>
        <p class="text-[13px] font-bold text-slate-400 uppercase tracking-widest mb-1">Analitik Bisnis</p>
        <h2 class="text-2xl sm:text-3xl font-bold text-slate-800 tracking-tight">Laporan & Performa Bisnis</h2>
    </div>
    <a href="{{ route('laporan.index') }}" class="shrink-0 px-5 py-2.5 rounded-xl text-[13px] font-bold bg-blue-600 text-white hover:bg-blue-700 transition-all shadow-[0_8px_20px_rgba(37,99,235,0.3)] flex items-center gap-2 transform hover:-translate-y-0.5">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
        Laporan Lengkap
    </a>
</div>

{{-- ===== STAT CARDS ===== --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">

    {{-- Omset --}}
    <div class="premium-glass p-5 rounded-2xl premium-hover relative overflow-hidden col-span-2 sm:col-span-1">
        <div class="absolute -right-6 -bottom-6 w-24 h-24 bg-blue-400/10 rounded-full blur-2xl"></div>
        <div class="flex items-center justify-between mb-3">
            <p class="text-[12px] font-bold text-slate-400 uppercase tracking-wide">Omset Bulan Ini</p>
            <div class="p-2 bg-blue-100 rounded-lg text-blue-600">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
        </div>
        <p class="text-[22px] font-black text-slate-800 leading-none mb-1">Rp {{ number_format($omsetBulanIni / 1000000, 1, ',', '') }} <span class="text-[14px] text-slate-500 font-bold">Jt</span></p>
        <div class="text-[12px] font-bold {{ $omsetGrowth >= 0 ? 'text-emerald-500' : 'text-red-500' }} flex items-center gap-1 mt-1">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="{{ $omsetGrowth >= 0 ? 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6' : 'M13 17h8m0 0V9m0 8l-8-8-4 4-6-6' }}"></path></svg>
            {{ $omsetGrowth >= 0 ? '+' : '' }}{{ $omsetGrowth }}% dari bulan lalu
        </div>
    </div>

    {{-- Pelanggan --}}
    <div class="premium-glass p-5 rounded-2xl premium-hover relative overflow-hidden">
        <div class="absolute -right-6 -bottom-6 w-24 h-24 bg-emerald-400/10 rounded-full blur-2xl"></div>
        <div class="flex items-center justify-between mb-3">
            <p class="text-[12px] font-bold text-slate-400 uppercase tracking-wide">Pelanggan</p>
            <div class="p-2 bg-emerald-100 rounded-lg text-emerald-600">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
            </div>
        </div>
        <p class="text-[22px] font-black text-slate-800 leading-none mb-1">{{ number_format($totalPelanggan, 0, ',', '.') }}</p>
        <p class="text-[12px] font-bold text-emerald-500">+{{ $pelangganBaruBulanIni }} bulan ini</p>
    </div>

    {{-- Order Hari Ini --}}
    <div class="premium-glass p-5 rounded-2xl premium-hover relative overflow-hidden">
        <div class="absolute -right-6 -bottom-6 w-24 h-24 bg-orange-400/10 rounded-full blur-2xl"></div>
        <div class="flex items-center justify-between mb-3">
            <p class="text-[12px] font-bold text-slate-400 uppercase tracking-wide">Order Hari Ini</p>
            <div class="p-2 bg-orange-100 rounded-lg text-orange-500">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
            </div>
        </div>
        <p class="text-[22px] font-black text-slate-800 leading-none mb-1">{{ $totalOrderHariIni }} <span class="text-[14px] text-slate-500 font-bold">Tiket</span></p>
        <p class="text-[12px] font-bold {{ $diffOrderHarian >= 0 ? 'text-emerald-500' : 'text-red-400' }}">{{ $diffOrderHarian >= 0 ? '+' : '' }}{{ $diffOrderHarian }} dari rata-rata</p>
    </div>

    {{-- Rating --}}
    <div class="premium-glass p-5 rounded-2xl premium-hover relative overflow-hidden">
        <div class="absolute -right-6 -bottom-6 w-24 h-24 bg-amber-400/10 rounded-full blur-2xl"></div>
        <div class="flex items-center justify-between mb-3">
            <p class="text-[12px] font-bold text-slate-400 uppercase tracking-wide">Rating</p>
            <div class="p-2 bg-amber-50 rounded-lg text-amber-500">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
            </div>
        </div>
        <p class="text-[22px] font-black text-slate-800 leading-none mb-1">{{ number_format($avgRating, 1) }} <span class="text-[14px] text-slate-500 font-bold">/ 5</span></p>
        <p class="text-[12px] font-medium text-slate-400">{{ $totalRatings }} ulasan</p>
    </div>

</div>

{{-- ===== GRAFIK REVENUE (12 BULAN) + LAYANAN TERLARIS ===== --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">

    {{-- Grafik Area 12 Bulan --}}
    <div class="lg:col-span-2 premium-glass p-6 rounded-[24px]">
        <div class="flex items-center justify-between mb-5 flex-wrap gap-3">
            <div>
                <h3 class="text-[16px] font-bold text-slate-800">Grafik Revenue</h3>
                <p class="text-[12px] text-slate-400 font-medium">Omset berdasarkan periode pembayaran lunas</p>
            </div>
            <div class="flex items-center bg-slate-100 rounded-xl p-1">
                <button @click="chartMode = 'monthly'"
                        :class="chartMode === 'monthly' ? 'bg-white text-blue-600 shadow-sm' : 'text-slate-500 hover:text-slate-700'"
                        class="px-3 py-1.5 text-[12px] font-bold rounded-lg transition-all">12 Bulan</button>
                <button @click="chartMode = 'weekly'"
                        :class="chartMode === 'weekly' ? 'bg-white text-blue-600 shadow-sm' : 'text-slate-500 hover:text-slate-700'"
                        class="px-3 py-1.5 text-[12px] font-bold rounded-lg transition-all">7 Hari</button>
            </div>
        </div>
        <div class="relative h-64">
            <canvas id="monthlyRevenueChart" x-show="chartMode === 'monthly'"></canvas>
            <canvas id="weeklyRevenueChart" x-show="chartMode === 'weekly'" style="display:none;"></canvas>
        </div>
    </div>

    {{-- Layanan Terlaris --}}
    <div class="premium-glass rounded-[24px] overflow-hidden flex flex-col">
        <div class="p-5 border-b border-white/60 bg-white/30">
            <h3 class="text-[16px] font-bold text-slate-800">Layanan Terlaris</h3>
            <p class="text-[12px] text-slate-400 font-medium">Top 5 kontributor omset</p>
        </div>
        <div class="p-5 flex-1 space-y-4">
            @php
                $barColors = [
                    'from-blue-500 to-blue-400',
                    'from-emerald-500 to-emerald-400',
                    'from-orange-500 to-orange-400',
                    'from-purple-500 to-purple-400',
                    'from-rose-500 to-rose-400',
                ];
                $badgeColors = [
                    'bg-blue-100 text-blue-600',
                    'bg-emerald-100 text-emerald-600',
                    'bg-orange-100 text-orange-600',
                    'bg-purple-100 text-purple-600',
                    'bg-rose-100 text-rose-600',
                ];
            @endphp
            @forelse($topServices as $idx => $ts)
            <div>
                <div class="flex justify-between items-center mb-1">
                    <span class="text-[13px] font-bold text-slate-700 flex items-center gap-1.5">
                        <span class="w-5 h-5 rounded-md {{ $badgeColors[$idx % 5] }} flex items-center justify-center text-[10px] font-black">{{ $idx+1 }}</span>
                        <span class="truncate max-w-[120px]">{{ $ts->service_name }}</span>
                    </span>
                    <span class="text-[12px] font-bold text-slate-600 shrink-0">Rp {{ number_format($ts->total_revenue/1000, 0, ',', '.') }}k</span>
                </div>
                <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                    <div class="bg-gradient-to-r {{ $barColors[$idx % 5] }} h-2 rounded-full transition-all duration-700" style="width: {{ $ts->percentage }}%"></div>
                </div>
                <p class="text-[10px] text-slate-400 font-medium mt-0.5">{{ $ts->total_orders }} transaksi · {{ $ts->percentage }}%</p>
            </div>
            @empty
            <p class="text-[13px] text-slate-400 font-bold py-4">Belum ada data layanan.</p>
            @endforelse
        </div>
    </div>

</div>

{{-- ===== RATING & ULASAN + PELANGGAN LOYAL ===== --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">

    {{-- Rating Overview --}}
    <div class="premium-glass rounded-[24px] overflow-hidden">
        <div class="p-5 border-b border-white/60 bg-white/30">
            <h3 class="text-[16px] font-bold text-slate-800">Ulasan & Rating Pelanggan</h3>
            <p class="text-[12px] text-slate-400 font-medium">{{ $totalRatings }} total ulasan diterima</p>
        </div>
        <div class="p-5">
            {{-- Score Big --}}
            <div class="flex items-center gap-5 mb-5 pb-5 border-b border-slate-100">
                <div class="text-center">
                    <p class="text-[48px] font-black text-amber-500 leading-none">{{ number_format($avgRating, 1) }}</p>
                    <div class="flex items-center justify-center gap-0.5 mt-1">
                        @for($i = 1; $i <= 5; $i++)
                            <svg class="w-4 h-4 {{ $i <= round($avgRating) ? 'text-amber-400' : 'text-slate-200' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        @endfor
                    </div>
                    <p class="text-[11px] text-slate-400 font-medium mt-1">dari 5 bintang</p>
                </div>
                {{-- Distribution --}}
                <div class="flex-1 space-y-1.5">
                    @foreach([5,4,3,2,1] as $star)
                    @php $count = $ratingDistribution[$star]->total ?? 0; $pct = $totalRatings > 0 ? round(($count / $totalRatings) * 100) : 0; @endphp
                    <div class="flex items-center gap-2">
                        <span class="text-[11px] font-bold text-slate-500 w-3">{{ $star }}</span>
                        <svg class="w-3 h-3 text-amber-400 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        <div class="flex-1 bg-slate-100 rounded-full h-2 overflow-hidden">
                            <div class="bg-amber-400 h-2 rounded-full" style="width: {{ $pct }}%"></div>
                        </div>
                        <span class="text-[10px] font-bold text-slate-400 w-4 text-right">{{ $count }}</span>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Recent Reviews --}}
            <h4 class="text-[12px] font-bold text-slate-400 uppercase tracking-wider mb-3">Ulasan Terbaru</h4>
            @forelse($recentReviews as $review)
            <div class="flex items-start gap-3 mb-3 pb-3 border-b border-slate-50 last:border-0 last:mb-0 last:pb-0">
                <img src="https://ui-avatars.com/api/?name={{ urlencode($review->reviewer_name ?? 'A') }}&background=random&rounded=true&bold=true&size=40" class="w-9 h-9 rounded-full shadow-sm ring-1 ring-white shrink-0" alt="">
                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between gap-2">
                        <p class="text-[13px] font-bold text-slate-700 truncate">{{ $review->reviewer_name ?? 'Anonim' }}</p>
                        <div class="flex items-center gap-0.5 shrink-0">
                            @for($s = 1; $s <= 5; $s++)
                                <svg class="w-3 h-3 {{ $s <= $review->rating ? 'text-amber-400' : 'text-slate-200' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            @endfor
                        </div>
                    </div>
                    @if($review->comment)
                    <p class="text-[12px] text-slate-500 font-medium mt-0.5 line-clamp-2">{{ $review->comment }}</p>
                    @else
                    <p class="text-[11px] text-slate-300 italic mt-0.5">Tanpa komentar</p>
                    @endif
                    <p class="text-[10px] text-slate-300 mt-1">{{ $review->created_at->diffForHumans() }} · {{ $review->order->order_code ?? '-' }}</p>
                </div>
            </div>
            @empty
            <div class="text-center py-6 text-slate-400">
                <div class="text-3xl mb-2">⭐</div>
                <p class="text-[13px] font-bold">Belum ada ulasan dari pelanggan.</p>
            </div>
            @endforelse
        </div>
    </div>

    {{-- Pelanggan Loyal --}}
    <div class="premium-glass rounded-[24px] overflow-hidden">
        <div class="p-5 border-b border-white/60 bg-white/30 flex justify-between items-center">
            <div>
                <h3 class="text-[16px] font-bold text-slate-800">Pelanggan Loyal</h3>
                <p class="text-[12px] text-slate-400 font-medium">Berdasarkan frekuensi & total transaksi</p>
            </div>
            <a href="{{ route('pelanggan.index') }}" class="text-[12px] font-bold text-blue-500 hover:underline">Lihat Semua</a>
        </div>
        <div class="divide-y divide-slate-50">
            @php $rankColors = ['from-yellow-400 to-amber-500', 'from-slate-300 to-slate-400', 'from-amber-600 to-amber-700']; @endphp
            @forelse($topCustomers as $idx => $tc)
            <div class="flex items-center gap-3 px-5 py-4 hover:bg-white/40 transition-colors">
                <div class="w-8 h-8 rounded-full bg-gradient-to-br {{ $rankColors[$idx] ?? 'from-slate-200 to-slate-300' }} text-white flex items-center justify-center font-black text-[12px] shadow-sm shrink-0">{{ $idx+1 }}</div>
                <img src="https://ui-avatars.com/api/?name={{ urlencode($tc->customer_name) }}&background=random&rounded=true&bold=true" alt="Avatar" class="w-10 h-10 rounded-full shadow-sm ring-2 ring-white shrink-0">
                <div class="flex-1 min-w-0">
                    <p class="font-bold text-slate-800 text-[14px] truncate">{{ $tc->customer_name }}</p>
                    <p class="text-[11px] text-blue-500 font-semibold">{{ $tc->total_trx }}x transaksi</p>
                </div>
                <div class="text-right shrink-0">
                    <p class="font-black text-[14px] text-slate-800">Rp {{ number_format($tc->total_spend/1000, 0, ',', '.') }}k</p>
                    <p class="text-[10px] text-slate-400">Total Spend</p>
                </div>
            </div>
            @empty
            <div class="py-12 text-center text-slate-400">
                <p class="text-[13px] font-bold">Belum ada data pelanggan.</p>
            </div>
            @endforelse
        </div>
    </div>

</div>

{{-- ===== STATUS ORDER SUMMARY ===== --}}
<div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-8">
    <div class="premium-glass rounded-2xl p-5 flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        </div>
        <div>
            <p class="text-[12px] font-bold text-slate-400 uppercase tracking-wide">Total Selesai (Diambil)</p>
            <p class="text-[24px] font-black text-emerald-600">{{ $totalSelesai }}</p>
        </div>
    </div>
    <div class="premium-glass rounded-2xl p-5 flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center shrink-0">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        </div>
        <div>
            <p class="text-[12px] font-bold text-slate-400 uppercase tracking-wide">Sedang Diproses</p>
            <p class="text-[24px] font-black text-amber-600">{{ $totalPending }}</p>
        </div>
    </div>
</div>

</div>{{-- end x-data --}}

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    Chart.defaults.font.family = "'Inter', 'Poppins', sans-serif";

    // ===== MONTHLY REVENUE CHART =====
    const ctxMonthly = document.getElementById('monthlyRevenueChart');
    if (ctxMonthly) {
        const gradM = ctxMonthly.getContext('2d').createLinearGradient(0, 0, 0, 256);
        gradM.addColorStop(0, 'rgba(37, 99, 235, 0.5)');
        gradM.addColorStop(1, 'rgba(37, 99, 235, 0.02)');

        new Chart(ctxMonthly, {
            type: 'bar',
            data: {
                labels: {!! $monthlyLabels->toJson() !!},
                datasets: [
                    {
                        label: 'Revenue (Rp000)',
                        data: {!! $monthlyRevenues->toJson() !!},
                        backgroundColor: gradM,
                        borderColor: '#2563EB',
                        borderWidth: 2,
                        borderRadius: 8,
                        borderSkipped: false,
                    },
                    {
                        label: 'Jumlah Order',
                        data: {!! $monthlyOrders->toJson() !!},
                        type: 'line',
                        borderColor: '#F59E0B',
                        backgroundColor: 'transparent',
                        borderWidth: 2.5,
                        pointBackgroundColor: '#fff',
                        pointBorderColor: '#F59E0B',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        tension: 0.4,
                        yAxisID: 'y2',
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: {
                        position: 'top', align: 'end',
                        labels: { usePointStyle: true, boxWidth: 8, color: '#64748b', font: { size: 11, weight: 'bold' } }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(255,255,255,0.97)',
                        titleColor: '#1e293b', bodyColor: '#475569',
                        borderColor: '#e2e8f0', borderWidth: 1, padding: 12, boxPadding: 6,
                        callbacks: {
                            label: function(ctx) {
                                if (ctx.datasetIndex === 0) return 'Revenue: Rp ' + ctx.parsed.y.toLocaleString('id-ID') + '.000';
                                return 'Order: ' + ctx.parsed.y + ' transaksi';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: { display: true, text: 'Revenue (Rp 000)', color: '#94a3b8', font: { size: 10 } },
                        ticks: { color: '#94a3b8', font: { size: 10 } },
                        grid: { color: 'rgba(0,0,0,0.04)', drawBorder: false }
                    },
                    y2: {
                        position: 'right',
                        beginAtZero: true,
                        title: { display: true, text: 'Jumlah Order', color: '#F59E0B', font: { size: 10 } },
                        ticks: { color: '#F59E0B', font: { size: 10 } },
                        grid: { display: false }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { color: '#64748b', font: { size: 10, weight: '600' } }
                    }
                }
            }
        });
    }

    // ===== WEEKLY REVENUE CHART =====
    const ctxWeekly = document.getElementById('weeklyRevenueChart');
    if (ctxWeekly) {
        const gradW = ctxWeekly.getContext('2d').createLinearGradient(0, 0, 0, 256);
        gradW.addColorStop(0, 'rgba(91, 141, 239, 0.55)');
        gradW.addColorStop(1, 'rgba(91, 141, 239, 0.02)');

        new Chart(ctxWeekly, {
            type: 'line',
            data: {
                labels: {!! $weeklyDates->toJson() !!},
                datasets: [{
                    label: 'Revenue Harian (Juta)',
                    data: {!! $weeklyRevenues->toJson() !!},
                    borderColor: '#5B8DEF',
                    backgroundColor: gradW,
                    borderWidth: 3,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#5B8DEF',
                    pointBorderWidth: 2,
                    pointRadius: 5,
                    pointHoverRadius: 7,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(255,255,255,0.97)',
                        titleColor: '#1e293b', bodyColor: '#475569',
                        borderColor: '#e2e8f0', borderWidth: 1, padding: 12,
                        callbacks: {
                            label: ctx => 'Revenue: Rp ' + ctx.parsed.y + ' Jt'
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { color: '#94a3b8', font: { size: 10 } },
                        grid: { color: 'rgba(0,0,0,0.04)', drawBorder: false }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { color: '#64748b', font: { size: 11, weight: '600' } }
                    }
                }
            }
        });
    }
});
</script>
@endpush
