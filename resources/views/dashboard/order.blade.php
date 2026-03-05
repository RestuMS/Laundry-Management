@extends('layouts.dashboard')

@section('title', 'Data Order')
@section('header_title', 'Semua Transaksi Order')

@section('content')

<!-- Custom Styles -->
<style>
/* Smooth Floating & Animations */
.glass-container {
    background: rgba(255, 255, 255, 0.75);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.9);
}
.btn-float {
    transition: all 0.2s ease;
}
.btn-float:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px -3px rgba(91, 141, 239, 0.3);
}

/* Scrollbar table */
.custom-scrollbar::-webkit-scrollbar {
    height: 6px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: rgba(143, 184, 255, 0.4);
    border-radius: 10px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: #5B8DEF;
}

td {
    color: #475569;
}
</style>

@php
    $pendingCount = $orders->where('status', 'Menunggu Konfirmasi')->count();
    $totalPending = \App\Models\Order::where('status', 'Menunggu Konfirmasi')->count();
@endphp

<div x-data="orderManager()">
    
    <!-- Pending Orders Alert -->
    @if($totalPending > 0)
    <div class="mb-5 bg-gradient-to-r from-amber-50 to-orange-50 border-2 border-amber-200 rounded-[20px] p-4 flex items-center gap-4 shadow-sm animate-pulse-slow">
        <div class="w-12 h-12 rounded-2xl bg-amber-100 flex items-center justify-center shrink-0">
            <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        </div>
        <div class="flex-1">
            <h4 class="font-bold text-amber-800 text-[15px]">{{ $totalPending }} Order Online Menunggu Konfirmasi</h4>
            <p class="text-[13px] text-amber-600 font-medium">Segera review dan konfirmasi/tolak pesanan online yang masuk.</p>
        </div>
        <div class="w-10 h-10 rounded-full bg-amber-500 text-white flex items-center justify-center font-black text-[16px] shadow-lg shadow-amber-200 shrink-0">
            {{ $totalPending }}
        </div>
    </div>
    @endif

    <!-- Top Bar: Search & Add -->
    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
        
        <!-- Live Realtime Search via Server Form -->
        <form action="{{ route('order.index') }}" method="GET" class="flex-1 w-full" x-ref="searchForm">
            <div class="w-full bg-white/70 backdrop-blur-md border border-white/80 rounded-[20px] focus-within:ring-4 focus-within:ring-[#4F8EF7]/10 transition-all shadow-[0_4px_20px_rgb(0,0,0,0.03)] flex items-center h-[56px] px-5">
                <svg class="w-5 h-5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                <input type="text" name="search" value="{{ $search ?? '' }}" x-model="searchQuery" @input.debounce.500ms="submitSearch()" placeholder="Cari trx, nama pelanggan, atau hp..." class="w-full h-full bg-transparent border-none focus:ring-0 text-[15px] text-slate-600 placeholder-slate-400 font-medium px-4" />
                @if(request('search'))
                <a href="{{ route('order.index') }}" class="w-6 h-6 rounded-full bg-slate-200 text-slate-500 hover:bg-slate-300 flex items-center justify-center shrink-0">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                </a>
                @endif
            </div>
        </form>

        <!-- Add Button -->
        <a href="{{ route('order.create') }}" class="shrink-0 h-[56px] bg-gradient-to-r from-[#4F8EF7] to-[#1E6DEB] hover:from-[#3a7ae6] hover:to-[#0f5bdd] text-white px-8 rounded-[20px] flex items-center gap-3 font-bold text-[15px] shadow-[0_4px_20px_rgba(79,142,247,0.4)] btn-float tracking-wide">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3.5" d="M12 4v16m8-8H4"></path></svg>
            Tambah Transaksi
        </a>

    </div>

    <!-- Filters Summary -->
    <div class="flex items-center justify-between mt-2 mb-4 px-2">
        <div class="flex items-center gap-2">
            <span class="w-3 h-3 rounded-full bg-[#10B981] shadow-[0_0_10px_rgba(16,185,129,0.5)]"></span>
            <span class="text-[13px] font-bold text-slate-600 tracking-wide uppercase">Realtime Database (Live)</span>
        </div>
        <div class="text-[14px]">
            <span class="font-bold text-[#4F8EF7]">{{ $orders->total() }}</span> <span class="text-slate-500 font-medium">Transaksi Terdata</span>
        </div>
    </div>

    <!-- Table Section Desktop -->
    <div class="hidden md:block glass-container rounded-[28px] p-2 overflow-hidden shadow-[0_8px_30px_rgb(0,0,0,0.04)] mb-8">
        <div class="overflow-x-auto custom-scrollbar pb-2">
            <table class="w-full text-left whitespace-nowrap border-collapse min-w-[1000px]">
                
                <thead>
                    <tr class="text-[13.5px] font-bold text-slate-400 uppercase tracking-widest border-b border-white/60">
                        <th class="py-5 px-6 rounded-tl-[24px]">Pelanggan</th>
                        <th class="py-5 px-4">Layanan & Detail</th>
                        <th class="py-5 px-4">Tanggal / Waktu</th>
                        <th class="py-5 px-4">Status & Alur</th>
                        <th class="py-5 px-4">Total Biaya</th>
                        <th class="py-5 px-6 rounded-tr-[24px] text-right">Tindakan</th>
                    </tr>
                </thead>

                <tbody class="text-[14.5px] font-medium text-slate-600 align-top">
                    
                    @forelse($orders as $order)
                    @php
                        // Pick random colors for avatar purely based on ID for fun variety
                        $colors = ['2563EB', '4F46E5', 'EC4899', '3B82F6', 'F59E0B', '8B5CF6'];
                        $bgColor = $colors[$order->id % count($colors)];
                    @endphp
                    <tr class="border-b border-white/40 hover:bg-white/50 transition-colors group">
                        
                        <td class="py-5 px-6 {{ $loop->last ? 'rounded-bl-[20px]' : '' }}">
                            <div class="flex items-center gap-4">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($order->customer_name) }}&background={{ $bgColor }}&color=fff&rounded=true&bold=true" class="w-12 h-12 rounded-[14px] shadow-sm border-2 border-white transform transition-transform group-hover:scale-105"/>
                                <div>
                                    <span class="font-bold text-slate-800 block text-[15.5px] mb-0.5">{{ $order->customer_name }}</span>
                                    <div class="flex items-center gap-1.5 flex-wrap">
                                        <span class="text-[12.5px] font-bold text-[#4F8EF7] bg-[#F0F5FF] px-2 py-0.5 rounded-md inline-block">
                                            {{ $order->order_code }}
                                        </span>
                                        @if($order->order_source === 'online')
                                        <span class="text-[10.5px] font-bold text-emerald-600 bg-emerald-50 border border-emerald-200 px-1.5 py-0.5 rounded-md inline-flex items-center gap-1">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path></svg>
                                            Online
                                        </span>
                                        @else
                                        <span class="text-[10.5px] font-bold text-slate-400 bg-slate-50 border border-slate-200 px-1.5 py-0.5 rounded-md inline-flex items-center gap-1">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                            Manual
                                        </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </td>
                        
                        <td class="py-5 px-4 w-[280px]">
                            @if($order->items && $order->items->count() > 0)
                                @foreach($order->items as $item)
                                <div class="mb-2 last:mb-0 border-b last:border-0 border-slate-100 pb-1 last:pb-0">
                                    <div class="font-bold text-slate-700 block text-[13px] whitespace-normal leading-tight">{{ $item->service_name }}</div>
                                    <div class="text-[12px] text-slate-400 font-medium">
                                        {{ $item->qty }} {{ $item->unit }} &times; {{ number_format($item->price, 0, ',', '.') }}
                                    </div>
                                </div>
                                @endforeach
                            @else
                                <div class="font-bold text-slate-700 block mb-1">{{ $order->service_name ?? 'Layanan' }}</div>
                                <div class="text-[13px] text-slate-400">Old Data</div>
                            @endif
                        </td>

                        <td class="py-5 px-4">
                            <div class="text-slate-600 block mb-1"><span class="font-semibold text-slate-700">Masuk:</span> {{ $order->created_at->format('d M y H:i') }}</div>
                            @if($order->estimated_finish)
                            <div class="text-[13px] text-[#F59E0B] flex items-center gap-1.5 font-semibold">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Est: {{ \Carbon\Carbon::parse($order->estimated_finish)->format('d M y') }}
                            </div>
                            @endif
                        </td>
                        
                        <td class="py-5 px-4 pt-4">
                            <!-- Dynamic Workflow Badge Status -->
                            @php
                                $badgeStyle = '';
                                if($order->status == 'Menunggu Konfirmasi') {
                                    $badgeStyle = 'bg-amber-50 text-amber-600 border border-amber-300 animate-pulse';
                                } elseif($order->status == 'Ditolak') {
                                    $badgeStyle = 'bg-red-50 text-red-500 border border-red-300';
                                } elseif($order->status == 'Diterima' || $order->status == 'Quality Control') {
                                    $badgeStyle = 'bg-slate-100 text-slate-600 border border-slate-200';
                                } elseif($order->status == 'Dicuci' || $order->status == 'Dikeringkan' || $order->status == 'Disetrika' || $order->status == 'Diproses') {
                                    $badgeStyle = 'bg-[#FFF4E5] text-[#F59E0B] border border-[#F59E0B]/20';
                                } elseif($order->status == 'Selesai' || $order->status == 'Diambil') {
                                    $badgeStyle = 'bg-[#E6F8F0] text-[#10B981] border border-[#10B981]/20';
                                } elseif($order->status == 'Belum Diambil') {
                                    $badgeStyle = 'bg-[#FEE2E2] text-[#EF4444] border border-[#EF4444]/20';
                                }
                            @endphp
                            <button @click="openStatusModal({{ $order->id }}, '{{ $order->status }}')" type="button" class="inline-block px-3.5 py-1.5 rounded-[10px] text-[13px] font-bold {{ $badgeStyle }} mb-2 hover:shadow-md hover:scale-105 transition-all text-left flex items-center justify-between gap-2 border-2 cursor-pointer w-36">
                                <span>{{ $order->status }}</span>
                                <svg class="w-3.5 h-3.5 opacity-60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7"></path></svg>
                            </button>
                            <br>
                            @if($order->payment_status == 'Belum Bayar' || $order->payment_status == 'Belum Lunas')
                                <button type="button" @click="openPaymentModal({{ $order->id }}, '{{ $order->payment_status }}')" class="hover:scale-105 transition-all text-[12px] font-bold text-red-500 bg-red-50 border border-red-100 px-2 py-1 rounded inline-flex items-center gap-1"><span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> Belum Bayar</button>
                            @elseif($order->payment_status == 'DP')
                                <button type="button" @click="openPaymentModal({{ $order->id }}, '{{ $order->payment_status }}')" class="hover:scale-105 transition-all text-[12px] font-bold text-orange-500 bg-orange-50 border border-orange-100 px-2 py-1 rounded inline-flex items-center gap-1"><span class="w-1.5 h-1.5 rounded-full bg-orange-500"></span> DP Sebagian</button>
                            @else
                                <button type="button" @click="openPaymentModal({{ $order->id }}, '{{ $order->payment_status }}')" class="hover:scale-105 transition-all text-[12px] font-bold text-[#10B981] bg-[#10B981]/10 border border-[#10B981]/20 px-2 py-1 rounded inline-flex items-center gap-1"><span class="w-1.5 h-1.5 rounded-full bg-[#10B981]"></span> {{ $order->payment_status }}</button>
                            @endif
                        </td>

                        <td class="py-5 px-4 font-black text-slate-700 text-[15px]">
                            Rp {{ number_format($order->total_price, 0, ',', '.') }}
                        </td>

                        <td class="py-5 px-6 text-right {{ $loop->last ? 'rounded-br-[20px]' : '' }}">
                            <div class="flex items-center justify-end gap-2">
                                @if($order->status === 'Menunggu Konfirmasi')
                                <!-- Approve Button -->
                                <button type="button" @click="confirmOnlineOrder({{ $order->id }})" class="h-10 px-4 rounded-xl bg-emerald-500 hover:bg-emerald-600 text-white font-bold text-[12.5px] shadow-sm flex items-center gap-1.5 transition-all hover:-translate-y-0.5" title="Konfirmasi Order">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                    Approve
                                </button>
                                <!-- Reject Button -->
                                <button type="button" @click="rejectOnlineOrder({{ $order->id }}, '{{ $order->order_code }}')" class="h-10 px-4 rounded-xl bg-red-500 hover:bg-red-600 text-white font-bold text-[12.5px] shadow-sm flex items-center gap-1.5 transition-all hover:-translate-y-0.5" title="Tolak Order">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    Tolak
                                </button>
                                @else
                                <!-- Print Button -->
                                <a href="{{ route('order.invoice', $order->id) }}" target="_blank" class="w-10 h-10 rounded-xl bg-white border border-slate-200 text-slate-500 hover:text-indigo-500 hover:border-indigo-500 transition-all flex items-center justify-center shadow-sm" title="Cetak Struk">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                </a>
                                
                                <!-- WA Button -->
                                @php
                                    $hasFonnteApi = !empty(env('FONNTE_TOKEN', \App\Models\Setting::where('key', 'fonnte_token')->value('value')));
                                @endphp
                                
                                @if($hasFonnteApi)
                                    <!-- WA API Auto Send Button -->
                                    <form action="{{ route('order.send_wa_invoice', $order->id) }}" method="POST" class="inline-block m-0 p-0">
                                        @csrf
                                        <button type="submit" onclick="return confirm('Kirim Notifikasi Invoice Tagihan ke WA Pelanggan menggunakan Sistem Otomatis sekarang?')" class="w-10 h-10 rounded-xl bg-green-50 border border-green-200 text-green-500 hover:bg-green-500 hover:text-white transition-all flex items-center justify-center shadow-sm" title="Kirim Tagihan (WA API Otomatis)">
                                            <svg class="w-[22px] h-[22px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                                        </button>
                                    </form>
                                @else
                                    <!-- Manual WA Web / App Fallback -->
                                    @php
                                        $phone = $order->customer_phone ? preg_replace('/^08/', '+628', $order->customer_phone) : null;
                                        $msg = "Halo Kak {$order->customer_name}, %0A%0ATerima kasih telah mencuci di Laundry Pro. Berikut ringkasan pesanan anda: %0A%0A🧾 *NO TRX:* {$order->order_code} %0A👕 *LAYANAN:* {$order->service_name} %0A💰 *TOTAL:* Rp " . number_format($order->total_price, 0, ',', '.') . " %0A💳 *STATUS BAYAR:* {$order->payment_status} %0A📦 *STATUS BARANG:* {$order->status} %0A%0AKakak bisa memantau cucian secara realtime di link berikut: %0A" . url('/track?q=' . $order->order_code);
                                        $waLink = $phone ? "https://wa.me/{$phone}?text={$msg}" : "javascript:alert('Nomor HP pelanggan tidak tersedia')";
                                    @endphp
                                    <a href="{{ $waLink }}" target="_blank" class="w-10 h-10 rounded-xl bg-green-50 border border-green-200 text-green-500 hover:bg-green-500 hover:text-white transition-all flex items-center justify-center shadow-sm" title="Kirim WA (Buka WA Web)">
                                        <svg class="w-[22px] h-[22px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                                    </a>
                                @endif

                                <!-- Edit -->
                                <a href="{{ route('order.edit', $order->id) }}" class="w-10 h-10 rounded-xl bg-[#F0F5FF] border border-[#4F8EF7]/30 text-[#4F8EF7] hover:bg-[#4F8EF7] hover:text-white transition-all font-semibold flex items-center justify-center shadow-sm text-[13.5px]">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                </a>

                                <!-- Photo Documentation -->
                                <button type="button" @click="openPhotoModal({{ $order->id }}, '{{ $order->order_code }}', {{ $order->photos->count() }})" class="w-10 h-10 rounded-xl border transition-all flex items-center justify-center shadow-sm relative {{ $order->photos->count() > 0 ? 'bg-purple-50 border-purple-200 text-purple-500 hover:bg-purple-500 hover:text-white' : 'bg-white border-slate-200 text-slate-400 hover:text-purple-500 hover:border-purple-300' }}" title="Dokumentasi Foto ({{ $order->photos->count() }})">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                    @if($order->photos->count() > 0)
                                    <span class="absolute -top-1.5 -right-1.5 w-5 h-5 rounded-full bg-purple-500 text-white text-[10px] font-black flex items-center justify-center shadow-sm">{{ $order->photos->count() }}</span>
                                    @endif
                                </button>

                                <!-- Delete -->
                                @if(auth()->user()?->role === 'admin')
                                <button type="button" @click="confirmDelete('{{ $order->id }}', '{{ $order->order_code }}', '{{ addslashes($order->customer_name) }}')" class="w-10 h-10 rounded-xl bg-red-50 border border-red-200 text-red-500 hover:bg-red-500 hover:text-white transition-all flex items-center justify-center shadow-sm">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                                @endif
                                @endif
                            </div>
                        </td>
                        
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-16 text-center">
                            <div class="flex flex-col items-center justify-center m-auto max-w-sm">
                                <div class="w-20 h-20 bg-slate-100 rounded-full flex items-center justify-center mb-4 text-slate-300">
                                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                                </div>
                                <h3 class="text-lg font-bold text-slate-700 mb-1">Traksaksi Belum Ditemukan</h3>
                                <p class="text-slate-400 font-medium text-[14px]">Klik tombol 'Tambah Transaksi' di atas untuk memulai pencatatan order pertama Anda.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse

                </tbody>
            </table>
        </div>

    </div>

    <!-- Mobile Card Section (Tampilan Khusus HP) -->
    <div class="md:hidden flex flex-col gap-4 mb-8 custom-scrollbar">
        @forelse($orders as $order)
        @php
            $colors = ['2563EB', '4F46E5', 'EC4899', '3B82F6', 'F59E0B', '8B5CF6'];
            $bgColor = $colors[$order->id % count($colors)];
            
            $badgeStyle = '';
            if($order->status == 'Menunggu Konfirmasi') {
                $badgeStyle = 'bg-amber-50 text-amber-600 border border-amber-300 animate-pulse';
            } elseif($order->status == 'Ditolak') {
                $badgeStyle = 'bg-red-50 text-red-500 border border-red-300';
            } elseif($order->status == 'Diterima' || $order->status == 'Quality Control') {
                $badgeStyle = 'bg-slate-100 text-slate-600 border border-slate-200';
            } elseif($order->status == 'Dicuci' || $order->status == 'Dikeringkan' || $order->status == 'Disetrika' || $order->status == 'Diproses') {
                $badgeStyle = 'bg-[#FFF4E5] text-[#F59E0B] border border-[#F59E0B]/20';
            } elseif($order->status == 'Selesai' || $order->status == 'Diambil') {
                $badgeStyle = 'bg-[#E6F8F0] text-[#10B981] border border-[#10B981]/20';
            } elseif($order->status == 'Belum Diambil') {
                $badgeStyle = 'bg-[#FEE2E2] text-[#EF4444] border border-[#EF4444]/20';
            }
        @endphp
        <div class="glass-container rounded-[20px] p-4 shadow-sm border {{ $order->status === 'Menunggu Konfirmasi' ? 'border-amber-300 ring-2 ring-amber-100' : 'border-white/60' }} relative overflow-hidden group">
            <!-- Approve/Reject Buttons for Mobile -->
            @if($order->status === 'Menunggu Konfirmasi')
            <div class="flex gap-2 mb-3">
                <button @click="confirmOnlineOrder({{ $order->id }})" class="flex-1 py-2.5 rounded-xl bg-emerald-500 text-white text-[12px] font-bold flex items-center justify-center gap-1 shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                    Konfirmasi
                </button>
                <button @click="rejectOnlineOrder({{ $order->id }}, '{{ $order->order_code }}')" class="flex-1 py-2.5 rounded-xl bg-red-500 text-white text-[12px] font-bold flex items-center justify-center gap-1 shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                    Tolak
                </button>
            </div>
            @endif
            <!-- Card Header -->
            <div class="flex justify-between items-start border-b border-slate-100/60 pb-3 mb-3 relative z-10">
                <div class="flex items-center gap-3">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode($order->customer_name) }}&background={{ $bgColor }}&color=fff&rounded=true&bold=true" class="w-10 h-10 rounded-full shadow-sm border-2 border-white"/>
                    <div>
                        <div class="font-bold text-[14.5px] text-slate-800 leading-tight mb-0.5">{{ $order->customer_name }}</div>
                        <div class="flex items-center gap-1 flex-wrap">
                            <div class="text-[11px] font-bold text-[#4F8EF7] bg-[#F0F5FF] px-2 py-0.5 rounded inline-flex items-center gap-1">
                                <i class="ph ph-hash"></i> {{ $order->order_code }}
                            </div>
                            @if($order->order_source === 'online')
                            <span class="text-[9px] font-bold text-emerald-600 bg-emerald-50 border border-emerald-200 px-1.5 py-0.5 rounded inline-flex items-center gap-0.5">
                                <i class="ph ph-globe text-[10px]"></i> Online
                            </span>
                            @endif
                        </div>
                    </div>
                </div>
                <!-- Status Workflow Mobile -->
                <button @click="openStatusModal({{ $order->id }}, '{{ $order->status }}')" type="button" class="inline-flex flex-col items-end gap-1 px-2.5 py-1 rounded-[8px] text-[11px] font-bold {{ $badgeStyle }} shadow-sm text-right shrink-0">
                    <span class="flex items-center gap-1">{{ $order->status }} <i class="ph ph-caret-down opacity-70"></i></span>
                </button>
            </div>

            <!-- Card Body: Layanan & Biaya -->
            <div class="grid grid-cols-2 gap-3 relative z-10">
                <div>
                    <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1 flex items-center gap-1"><i class="ph ph-t-shirt"></i> Layanan</div>
                    @if($order->items && $order->items->count() > 0)
                        <div class="font-bold text-[13px] text-slate-700 leading-snug break-words pr-2">
                            {{ mb_strimwidth(is_object($order->items->first()) ? $order->items->first()->service_name : $order->items[0]->service_name, 0, 20, '...') }}
                        </div>
                        @if($order->items->count() > 1)
                            <div class="text-[11px] text-[#4F8EF7] font-bold mt-0.5">+{{ $order->items->count() - 1 }} Item Lain</div>
                        @endif
                    @else
                        <div class="font-bold text-[13px] text-slate-700">{{ mb_strimwidth($order->service_name ?? 'Layanan', 0, 20, '...') }}</div>
                    @endif
                </div>
                
                <div class="text-right">
                    <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1 flex items-center justify-end gap-1"><i class="ph ph-wallet"></i> Tagihan</div>
                    <div class="font-black text-[14px] text-slate-800 mb-1.5 whitespace-nowrap">Rp {{ number_format($order->total_price, 0, ',', '.') }}</div>
                    @if($order->payment_status == 'Belum Bayar' || $order->payment_status == 'Belum Lunas')
                        <button type="button" @click="openPaymentModal({{ $order->id }}, '{{ $order->payment_status }}')" class="text-[10px] font-bold text-red-500 bg-red-50 border border-red-100 px-2 py-0.5 rounded inline-flex items-center gap-1"><span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> Belum Bayar</button>
                    @elseif($order->payment_status == 'DP')
                        <button type="button" @click="openPaymentModal({{ $order->id }}, '{{ $order->payment_status }}')" class="text-[10px] font-bold text-orange-500 bg-orange-50 border border-orange-100 px-2 py-0.5 rounded inline-flex items-center gap-1"><span class="w-1.5 h-1.5 rounded-full bg-orange-500"></span> DP Sebagian</button>
                    @else
                        <button type="button" @click="openPaymentModal({{ $order->id }}, '{{ $order->payment_status }}')" class="text-[10px] font-bold text-[#10B981] bg-[#10B981]/10 border border-[#10B981]/20 px-2 py-0.5 rounded inline-flex items-center gap-1"><span class="w-1.5 h-1.5 rounded-full bg-[#10B981]"></span> {{ $order->payment_status }}</button>
                    @endif
                </div>
            </div>

            <!-- Card Footer: Actions -->
            <div class="mt-4 pt-3 border-t border-slate-100/60 flex items-center justify-between gap-2 overflow-x-auto no-scrollbar pb-1">
                <div class="text-[11px] font-bold text-slate-400 flex items-center gap-1 shrink-0">
                    <i class="ph-fill ph-clock"></i> {{ $order->created_at->format('d/m/y') }}
                </div>

                <div class="flex items-center gap-2 justify-end w-full">
                    <!-- WA Autopilot -->
                    @php
                        $hasFonnteApi = !empty(env('FONNTE_TOKEN', \App\Models\Setting::where('key', 'fonnte_token')->value('value')));
                        $phone = $order->customer_phone ? preg_replace('/^08/', '+628', $order->customer_phone) : null;
                        $msg = "Halo Kak {$order->customer_name}, %0A%0ATerima kasih telah mencuci di Laundry Pro. Berikut pesanan anda: %0A🧾 *NO TRX:* {$order->order_code} %0A💰 *TOTAL:* Rp " . number_format($order->total_price, 0, ',', '.') . " %0A💳 *BAYAR:* {$order->payment_status} %0A📦 *STATUS:* {$order->status} %0A%0ACek realtime: %0A" . url('/track?q=' . $order->order_code);
                        $waLink = $phone ? "https://wa.me/{$phone}?text={$msg}" : "javascript:alert('Nomor HP pelanggan tidak tersedia')";
                    @endphp
                    
                    @if($hasFonnteApi)
                    <form action="{{ route('order.send_wa_invoice', $order->id) }}" method="POST" class="inline-block m-0 p-0 shrink-0">
                        @csrf
                        <button type="submit" onclick="return confirm('Kirim WA Otomatis?')" class="w-8 h-8 rounded-lg bg-[#E6F8F0] border border-[#10B981]/20 text-[#10B981] flex items-center justify-center font-bold">
                            <i class="ph-fill ph-whatsapp-logo text-[18px]"></i>
                        </button>
                    </form>
                    @else
                    <a href="{{ $waLink }}" target="_blank" class="w-8 h-8 rounded-lg bg-[#E6F8F0] border border-[#10B981]/20 text-[#10B981] flex items-center justify-center font-bold shrink-0">
                        <i class="ph-fill ph-whatsapp-logo text-[18px]"></i>
                    </a>
                    @endif

                    <!-- Print -->
                    <a href="{{ route('order.invoice', $order->id) }}" target="_blank" class="w-8 h-8 rounded-lg bg-white border border-slate-200 text-slate-500 flex items-center justify-center font-bold shrink-0 shadow-sm">
                        <i class="ph-fill ph-printer text-[16px]"></i>
                    </a>

                    <!-- Edit -->
                    <a href="{{ route('order.edit', $order->id) }}" class="h-8 px-3 rounded-lg bg-[#F0F5FF] border border-[#4F8EF7]/30 text-[#4F8EF7] flex items-center justify-center font-bold text-[12px] gap-1.5 shrink-0 shadow-sm">
                        <i class="ph-fill ph-pencil-simple text-[14px]"></i> Edit
                    </a>

                    <!-- Delete (Admin) -->
                    @if(auth()->user()?->role === 'admin')
                    <button type="button" @click="confirmDelete('{{ $order->id }}', '{{ $order->order_code }}', '{{ addslashes($order->customer_name) }}')" class="w-8 h-8 rounded-lg bg-[#FEE2E2] border border-[#EF4444]/20 text-[#EF4444] flex items-center justify-center font-bold shrink-0 shadow-sm">
                        <i class="ph-fill ph-trash text-[18px]"></i>
                    </button>
                    @endif
                </div>
            </div>
        </div>
        @empty
        <div class="py-12 text-center glass-container rounded-[20px]">
            <div class="flex flex-col items-center justify-center max-w-xs mx-auto">
                <i class="ph-fill ph-receipt text-[45px] text-slate-300 mb-3"></i>
                <h3 class="text-[16px] font-bold text-slate-600 mb-1">Belum Ada Transaksi</h3>
            </div>
        </div>
        @endforelse

        <!-- Mobile Pagination -->
        @if($orders->hasPages())
        <div class="pt-2">
            {{ $orders->links('pagination::tailwind') }}
        </div>
        @endif
    </div>

    <!-- MODERN POPUP DELETE MODAL -->
    <div x-show="deleteModalOpen" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center overflow-hidden" style="display: none;">
        <!-- Backdrop Blur -->
        <div x-show="deleteModalOpen" x-transition.opacity class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm"></div>
        
        <!-- Modal Content -->
        <div x-show="deleteModalOpen"
            @click.away="deleteModalOpen = false" 
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-8 scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 scale-95"
            class="relative w-full max-w-md bg-white rounded-[24px] shadow-2xl p-8 z-10 mx-4 border border-white/80 overflow-hidden">
            
            <div class="absolute -top-12 -right-12 w-32 h-32 bg-red-50 rounded-full blur-2xl"></div>
            
            <div class="relative z-10 text-center">
                <!-- Delete Icon Circle -->
                <div class="w-20 h-20 bg-red-100 text-red-500 rounded-full flex items-center justify-center mx-auto mb-6 shadow-inner ring-8 ring-red-50 relative overflow-hidden">
                    <svg class="w-10 h-10 animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                </div>
                
                <h3 class="text-2xl font-bold text-slate-800 mb-2">Hapus Transaksi?</h3>
                <p class="text-slate-500 text-[14.5px] leading-relaxed mb-8">
                    Anda yakin ingin memindahkan order <strong x-text="deleteCode" class="text-slate-800 font-bold"></strong> milik <strong x-text="deleteName" class="text-slate-800 font-bold"></strong> ke folder temp? Aksi ini diotorisasi untuk Admin.
                </p>

                <div class="flex gap-3 w-full">
                    <button type="button" @click="deleteModalOpen = false" class="flex-1 h-12 rounded-[14px] bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold text-[14px] transition-colors">
                        Batal
                    </button>
                    <!-- Form Submission -->
                    <form :action="deleteUrl" method="POST" class="flex-1">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full h-12 rounded-[14px] bg-red-500 hover:bg-red-600 text-white font-bold text-[14px] shadow-[0_4px_15px_rgba(239,68,68,0.4)] transition-all transform hover:-translate-y-0.5">
                            Ya, Hapus Data
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- MODERN STATUS WORKFLOW MODAL -->
    <div x-show="statusModalOpen" x-cloak class="fixed inset-0 z-[120] flex items-center justify-center overflow-hidden" style="display: none;">
        <div x-show="statusModalOpen" x-transition.opacity class="absolute inset-0 bg-slate-900/30 backdrop-blur-sm"></div>
        
        <div x-show="statusModalOpen"
            @click.away="statusModalOpen = false" 
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-8 scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 scale-95"
            class="relative w-full max-w-[400px] bg-white rounded-[24px] shadow-2xl p-6 z-10 mx-4 border border-slate-100/80 flex flex-col max-h-[90vh]">
            
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h3 class="text-xl font-bold text-slate-800 tracking-tight">Workflow Laundry</h3>
                    <p class="text-[13px] text-slate-500 font-medium">Ubah tahapan pengerjaan saat ini.</p>
                </div>
                <button type="button" @click="statusModalOpen = false" class="text-slate-400 hover:text-slate-600 bg-slate-100 p-2 rounded-full transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <div class="flex-1 overflow-y-auto custom-scrollbar -mr-2 pr-2">
                <div class="space-y-3 pb-2 relative">
                    <!-- The dynamic progress connection line -->
                    <div class="absolute inset-y-0 left-[35px] w-0.5 bg-slate-100/80 -z-10 rounded-full mt-6 mb-6"></div>

                    <template x-for="(status, index) in statuses" :key="index">
                        <div @click="setStatus(status.name)" 
                             class="group cursor-pointer border rounded-[16px] p-3 flex items-center transition-all duration-200"
                             :class="currentStatus === status.name ? 'border-[#3B82F6] bg-[#EFF6FF] shadow-sm' : 'border-slate-100 hover:border-slate-300 hover:bg-slate-50'">
                            
                            <!-- Custom Radio Box -->
                            <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center mr-4 shrink-0 transition-colors"
                                 :class="currentStatus === status.name ? 'border-[#3B82F6] bg-white' : 'border-slate-300 group-hover:border-[#3B82F6]'">
                                 <div class="w-2.5 h-2.5 rounded-full bg-[#3B82F6] transition-opacity duration-200"
                                      :class="currentStatus === status.name ? 'opacity-100' : 'opacity-0'"></div>
                            </div>
                            
                            <!-- Icon -->
                            <div class="flex items-center justify-center w-10 h-10 rounded-[12px] shrink-0 mr-4 transition-colors shadow-[0_2px_10px_rgb(0,0,0,0.02)]"
                                 :class="currentStatus === status.name ? status.bgActive + ' ' + status.textActive : 'bg-white border border-slate-100 text-slate-400 group-hover:bg-slate-100 group-hover:text-slate-600'">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" :d="status.icon"></path></svg>
                            </div>

                            <!-- Label -->
                            <div class="font-bold flex-1 text-[15px]"
                                 :class="currentStatus === status.name ? 'text-slate-800 tracking-tight' : 'text-slate-600'">
                                <span x-text="status.name"></span>
                            </div>

                            <!-- Spinner if updating -->
                            <div x-show="isUpdating && currentStatus === status.name" class="shrink-0 pl-2">
                                <svg class="animate-spin w-4 h-4 text-[#3B82F6]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Warning Notice -->
            <div class="mt-4 bg-[#FFF4E5] border border-[#F59E0B]/20 rounded-[14px] p-3 flex gap-2.5">
                <svg class="w-4 h-4 text-[#F59E0B] shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <div class="text-[12px] text-[#D97706] font-medium leading-tight">
                    Mengubah status otomatis akan mengirim pembaruan via notif WhatsApp ke pelanggan (jika nomor tercatat).
                </div>
            </div>

        </div>
    </div>

    <!-- MODERN PAYMENT MANAGEMENT MODAL -->
    <div x-show="paymentModalOpen" x-cloak class="fixed inset-0 z-[120] flex items-center justify-center overflow-hidden" style="display: none;">
        <div x-show="paymentModalOpen" x-transition.opacity class="absolute inset-0 bg-slate-900/30 backdrop-blur-sm"></div>
        
        <div x-show="paymentModalOpen"
            @click.away="paymentModalOpen = false" 
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-8 scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 scale-95"
            class="relative w-full max-w-[460px] bg-white rounded-[24px] shadow-2xl p-6 z-10 mx-4 border border-slate-100/80 flex flex-col max-h-[90vh]">
            
            <div class="flex justify-between items-center mb-5">
                <div>
                    <h3 class="text-xl font-bold text-slate-800 tracking-tight">Pembayaran</h3>
                    <p class="text-[13px] text-slate-500 font-medium">Catat pembayaran DP / cicilan / lunas</p>
                </div>
                <button type="button" @click="paymentModalOpen = false" class="text-slate-400 hover:text-slate-600 bg-slate-100 p-2 rounded-full transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <!-- Payment Summary Card -->
            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-2xl p-4 mb-5 border border-blue-100">
                <div class="grid grid-cols-3 gap-3 text-center">
                    <div>
                        <p class="text-[10px] font-bold text-slate-400 uppercase mb-1">Total</p>
                        <p class="text-[14px] font-black text-slate-700" x-text="'Rp ' + formatRupiah(paymentData.grand_total)"></p>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-green-500 uppercase mb-1">Dibayar</p>
                        <p class="text-[14px] font-black text-green-600" x-text="'Rp ' + formatRupiah(paymentData.total_paid)"></p>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-red-400 uppercase mb-1">Sisa</p>
                        <p class="text-[14px] font-black" :class="paymentData.remaining > 0 ? 'text-red-500' : 'text-green-600'" x-text="'Rp ' + formatRupiah(paymentData.remaining)"></p>
                    </div>
                </div>
            </div>

            <div class="flex-1 overflow-y-auto custom-scrollbar -mr-2 pr-2">
                <!-- Payment History -->
                <div x-show="paymentData.payments && paymentData.payments.length > 0" class="mb-5">
                    <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-3">Riwayat Pembayaran</p>
                    <div class="space-y-2">
                        <template x-for="pay in paymentData.payments" :key="pay.id">
                            <div class="flex items-center justify-between bg-slate-50 rounded-xl px-4 py-3 border border-slate-100">
                                <div>
                                    <p class="text-[13px] font-bold text-slate-700" x-text="'Rp ' + formatRupiah(pay.amount)"></p>
                                    <p class="text-[11px] text-slate-400" x-text="pay.payment_method + ' • ' + pay.created_at + ' • ' + pay.received_by"></p>
                                    <p x-show="pay.note" class="text-[11px] text-slate-400 italic" x-text="pay.note"></p>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Add Payment Form -->
                <div x-show="paymentData.remaining > 0" class="border-t border-slate-100 pt-5">
                    <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-3">Tambah Pembayaran</p>
                    
                    <div class="space-y-3">
                        <div>
                            <label class="text-[12px] font-bold text-slate-600 mb-1 block">Jumlah (Rp)</label>
                            <input type="number" x-model="newPayment.amount" :max="paymentData.remaining" min="1" class="w-full px-4 py-3 rounded-xl border border-slate-200 text-[14px] font-bold focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none" :placeholder="'Max: ' + formatRupiah(paymentData.remaining)">
                        </div>
                        <div>
                            <label class="text-[12px] font-bold text-slate-600 mb-1 block">Metode Bayar</label>
                            <select x-model="newPayment.payment_method" class="w-full px-4 py-3 rounded-xl border border-slate-200 text-[13px] font-medium focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none bg-white">
                                <option value="Cash">Cash</option>
                                <option value="Transfer">Transfer Bank</option>
                                <option value="QRIS">QRIS</option>
                                <option value="E-Wallet">E-Wallet</option>
                            </select>
                        </div>
                        <div>
                            <label class="text-[12px] font-bold text-slate-600 mb-1 block">Catatan (opsional)</label>
                            <input type="text" x-model="newPayment.note" class="w-full px-4 py-3 rounded-xl border border-slate-200 text-[13px] font-medium focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none" placeholder="Contoh: DP pertama, cicilan ke-2, dll.">
                        </div>
                        <button @click="submitPayment()" :disabled="isSubmittingPayment" class="w-full py-3.5 bg-primary text-white rounded-xl font-bold text-[14px] shadow-[0_4px_15px_rgba(59,130,246,0.3)] hover:bg-blue-700 transition-all flex items-center justify-center gap-2 disabled:opacity-50">
                            <svg x-show="isSubmittingPayment" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            <i class="ph ph-plus-circle text-lg" x-show="!isSubmittingPayment"></i>
                            <span x-text="isSubmittingPayment ? 'Menyimpan...' : 'Catat Pembayaran'"></span>
                        </button>
                    </div>
                </div>

                <!-- Fully Paid Notice -->
                <div x-show="paymentData.remaining <= 0 && paymentData.total_paid > 0" class="bg-green-50 border border-green-200 rounded-2xl p-4 text-center mt-4">
                    <i class="ph-fill ph-check-circle text-3xl text-green-500 mb-2"></i>
                    <p class="text-[14px] font-bold text-green-700">Pembayaran Lunas!</p>
                    <p class="text-[12px] text-green-600">Seluruh tagihan sudah terbayar.</p>
                </div>
            </div>

            <!-- Quick Status Change (legacy) -->
            <div class="mt-4 pt-4 border-t border-slate-100">
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-2">Ubah Status Manual</p>
                <div class="flex gap-2">
                    <template x-for="(payStatus, index) in paymentStatuses" :key="index">
                        <button @click="setPaymentStatus(payStatus.name)"
                                class="flex-1 py-2.5 rounded-xl text-[12px] font-bold border-2 transition-all"
                                :class="currentPaymentStatus === payStatus.name 
                                    ? 'border-blue-500 bg-blue-50 text-blue-700' 
                                    : 'border-slate-100 text-slate-500 hover:border-slate-300'">
                            <span x-text="payStatus.name"></span>
                        </button>
                    </template>
                </div>
            </div>
        </div>
    </div>

    <!-- Photo Documentation Modal -->
    <div x-show="photoModalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4" @keydown.escape.window="photoModalOpen = false">
        <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" @click="photoModalOpen = false"></div>
        <div class="relative bg-white rounded-[24px] shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-hidden flex flex-col" @click.stop>
            <!-- Header -->
            <div class="p-6 pb-4 border-b border-slate-100">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-[18px] font-black text-slate-800 flex items-center gap-2">
                            <svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            Dokumentasi Foto
                        </h3>
                        <p class="text-[13px] text-slate-400 font-medium mt-0.5">Order: <span class="text-slate-600 font-bold" x-text="photoOrderCode"></span></p>
                    </div>
                    <button @click="photoModalOpen = false" class="w-10 h-10 rounded-xl bg-slate-100 hover:bg-slate-200 flex items-center justify-center transition-colors">
                        <svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
            </div>

            <div class="overflow-y-auto flex-1 p-6">
                <!-- Upload Section -->
                <div class="mb-6">
                    <p class="text-[12px] font-bold text-slate-400 uppercase tracking-widest mb-3">Upload Foto Baru</p>
                    
                    <!-- Photo Type -->
                    <div class="flex gap-2 mb-3">
                        <template x-for="t in ['masuk', 'proses', 'selesai']" :key="t">
                            <button @click="photoType = t" 
                                    class="flex-1 py-2.5 rounded-xl text-[12px] font-bold border-2 transition-all"
                                    :class="photoType === t 
                                        ? (t === 'masuk' ? 'border-blue-500 bg-blue-50 text-blue-700' : (t === 'proses' ? 'border-amber-500 bg-amber-50 text-amber-700' : 'border-emerald-500 bg-emerald-50 text-emerald-700'))
                                        : 'border-slate-100 text-slate-500 hover:border-slate-300'">
                                <span x-text="t === 'masuk' ? '📥 Saat Masuk' : (t === 'proses' ? '🔄 Saat Proses' : '✅ Saat Selesai')"></span>
                            </button>
                        </template>
                    </div>

                    <!-- Caption -->
                    <input type="text" x-model="photoCaption" placeholder="Keterangan foto (opsional)..." class="w-full px-4 py-3 rounded-xl border border-slate-200 text-[13px] font-medium focus:ring-2 focus:ring-purple-500/20 focus:border-purple-400 mb-3">

                    <!-- Drop Zone -->
                    <div class="border-2 border-dashed rounded-2xl p-6 text-center transition-colors cursor-pointer"
                         :class="isDragging ? 'border-purple-400 bg-purple-50' : 'border-slate-200 hover:border-purple-300 bg-slate-50/50'"
                         @dragover.prevent="isDragging = true"
                         @dragleave.prevent="isDragging = false"
                         @drop.prevent="handlePhotoDrop($event)"
                         @click="$refs.photoInput.click()">
                        <input type="file" x-ref="photoInput" accept="image/*" multiple class="hidden" @change="handlePhotoSelect($event)">
                        <svg class="w-10 h-10 mx-auto mb-2 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        <p class="text-[13px] font-bold text-slate-500">Klik atau drag foto ke sini</p>
                        <p class="text-[11px] text-slate-400 font-medium mt-1">JPG, PNG, WebP • Max 5MB/foto • Max 5 foto</p>
                    </div>

                    <!-- Preview Selected -->
                    <template x-if="selectedPhotos.length > 0">
                        <div class="mt-3">
                            <div class="flex flex-wrap gap-2 mb-3">
                                <template x-for="(file, i) in selectedPhotos" :key="i">
                                    <div class="relative w-20 h-20 rounded-xl overflow-hidden border-2 border-purple-200">
                                        <img :src="photoPreviewUrls[i]" class="w-full h-full object-cover">
                                        <button @click="removeSelectedPhoto(i)" class="absolute top-0.5 right-0.5 w-5 h-5 rounded-full bg-red-500 text-white flex items-center justify-center text-[10px] font-bold">✕</button>
                                    </div>
                                </template>
                            </div>
                            <button @click="uploadPhotos()" :disabled="isUploadingPhotos" 
                                    class="w-full py-3 rounded-xl bg-gradient-to-r from-purple-500 to-indigo-600 text-white font-bold text-[13px] hover:shadow-lg transition-all disabled:opacity-50 flex items-center justify-center gap-2">
                                <template x-if="isUploadingPhotos">
                                    <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                </template>
                                <span x-text="isUploadingPhotos ? 'Mengupload...' : ('Upload ' + selectedPhotos.length + ' Foto')"></span>
                            </button>
                        </div>
                    </template>
                </div>

                <!-- Existing Photos Gallery -->
                <div>
                    <p class="text-[12px] font-bold text-slate-400 uppercase tracking-widest mb-3">Galeri Foto (<span x-text="orderPhotos.length"></span>)</p>
                    
                    <template x-if="isLoadingPhotos">
                        <div class="text-center py-8">
                            <svg class="w-8 h-8 mx-auto animate-spin text-purple-400" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            <p class="text-[13px] text-slate-400 font-medium mt-2">Memuat foto...</p>
                        </div>
                    </template>

                    <template x-if="!isLoadingPhotos && orderPhotos.length === 0">
                        <div class="text-center py-8 bg-slate-50 rounded-2xl">
                            <svg class="w-12 h-12 mx-auto text-slate-200 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            <p class="text-[13px] text-slate-400 font-medium">Belum ada foto dokumentasi</p>
                        </div>
                    </template>

                    <template x-if="!isLoadingPhotos && orderPhotos.length > 0">
                        <div>
                            <template x-for="type in ['masuk', 'proses', 'selesai']" :key="type">
                                <div x-show="orderPhotos.filter(p => p.type === type).length > 0" class="mb-4">
                                    <p class="text-[12px] font-bold mb-2 flex items-center gap-1.5"
                                       :class="type === 'masuk' ? 'text-blue-500' : (type === 'proses' ? 'text-amber-500' : 'text-emerald-500')">
                                        <span x-text="type === 'masuk' ? '📥 Saat Masuk' : (type === 'proses' ? '🔄 Saat Proses' : '✅ Saat Selesai')"></span>
                                    </p>
                                    <div class="grid grid-cols-3 gap-2">
                                        <template x-for="photo in orderPhotos.filter(p => p.type === type)" :key="photo.id">
                                            <div class="relative group rounded-xl overflow-hidden border border-slate-100 shadow-sm aspect-square">
                                                <img :src="photo.url" :alt="photo.caption || 'Foto'" class="w-full h-full object-cover cursor-pointer hover:scale-105 transition-transform" @click="lightboxUrl = photo.url; lightboxOpen = true">
                                                <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-black/60 to-transparent p-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                                    <p class="text-[10px] text-white font-medium truncate" x-text="photo.caption || photo.created_at"></p>
                                                </div>
                                                <button @click="deletePhoto(photo.id)" class="absolute top-1.5 right-1.5 w-6 h-6 rounded-full bg-red-500 text-white flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity text-[11px] font-bold shadow-lg hover:bg-red-600">✕</button>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>

    <!-- Lightbox -->
    <div x-show="lightboxOpen" x-cloak class="fixed inset-0 z-[60] flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm" @click="lightboxOpen = false" @keydown.escape.window="lightboxOpen = false">
        <img :src="lightboxUrl" class="max-w-full max-h-[90vh] rounded-2xl shadow-2xl" @click.stop>
        <button @click="lightboxOpen = false" class="absolute top-6 right-6 w-10 h-10 rounded-full bg-white/20 text-white flex items-center justify-center hover:bg-white/40 transition-colors">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>
    </div>

</div>

<script>
function orderManager() {
    return {
        searchQuery: {!! json_encode(request('search', '')) !!},
        deleteModalOpen: false,
        deleteId: '',
        deleteCode: '',
        deleteName: '',
        deleteUrl: '',
        
        statusModalOpen: false,
        updateId: '',
        currentStatus: '',
        isUpdating: false,
        
        paymentModalOpen: false,
        updatePaymentId: '',
        currentPaymentStatus: '',
        isUpdatingPayment: false,
        isSubmittingPayment: false,
        
        // Payment data from API
        paymentData: {
            grand_total: 0,
            total_paid: 0,
            remaining: 0,
            payments: [],
        },
        newPayment: {
            amount: '',
            payment_method: 'Cash',
            note: '',
        },

        // Photo documentation
        photoModalOpen: false,
        photoOrderId: null,
        photoOrderCode: '',
        photoType: 'masuk',
        photoCaption: '',
        selectedPhotos: [],
        photoPreviewUrls: [],
        isUploadingPhotos: false,
        isLoadingPhotos: false,
        isDragging: false,
        orderPhotos: [],
        lightboxOpen: false,
        lightboxUrl: '',
        
        statuses: [
            { name: 'Diterima', icon: 'M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4', bgActive: 'bg-slate-100', textActive: 'text-slate-700' },
            { name: 'Dicuci', icon: 'M5 4h14a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2z M12 17a5 5 0 1 0 0-10 5 5 0 0 0 0 10z M12 15a3 3 0 1 0 0-6 3 3 0 0 0 0 6z M7 8h2', bgActive: 'bg-blue-100', textActive: 'text-blue-600' },
            { name: 'Dikeringkan', icon: 'M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z', bgActive: 'bg-amber-100', textActive: 'text-amber-600' },
            { name: 'Disetrika', icon: 'M4 17h14c1.5 0 3-1.5 3-3 0-3-3-5-6-5H4v8z M4 9V6c0-1.5 1.5-3 3-3h5c1.5 0 3 1.5 3 3v3', bgActive: 'bg-pink-100', textActive: 'text-pink-600' },
            { name: 'Quality Control', icon: 'M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z', bgActive: 'bg-indigo-100', textActive: 'text-indigo-600' },
            { name: 'Selesai', icon: 'M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z', bgActive: 'bg-emerald-100', textActive: 'text-emerald-600' },
            { name: 'Diambil', icon: 'M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z M9 15l2 2 4-4', bgActive: 'bg-teal-100', textActive: 'text-teal-600' }
        ],
        
        paymentStatuses: [
            { name: 'Belum Bayar', icon: 'M10 14H5.236a2 2 0 01-1.789-2.894l3.5-7A2 2 0 018.736 3h4.018a2 2 0 01.485.06l3.76.94m-7 10v5a2 2 0 002 2h.096c.5 0 .905-.405.905-.904 0-.715.211-1.413.608-2.008L17 13V4m-7 10h2m5-10h2a2 2 0 012 2v6a2 2 0 01-2 2h-2.5', bgActive: 'bg-red-100', textActive: 'text-red-500' },
            { name: 'DP', icon: 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z', bgActive: 'bg-orange-100', textActive: 'text-orange-500' },
            { name: 'Lunas', icon: 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z', bgActive: 'bg-emerald-100', textActive: 'text-emerald-500' },
        ],

        formatRupiah(num) {
            return new Intl.NumberFormat('id-ID').format(num || 0);
        },

        submitSearch() {
            this.$refs.searchForm.submit();
        },

        confirmDelete(id, code, name) {
            this.deleteId = id;
            this.deleteCode = code;
            this.deleteName = name;
            const baseUrl = "{{ url('order') }}";
            this.deleteUrl = `${baseUrl}/${id}`;
            this.deleteModalOpen = true;
        },

        async confirmOnlineOrder(orderId) {
            if (!confirm('Konfirmasi order ini? Pesanan akan masuk ke antrian proses.')) return;
            
            try {
                const res = await fetch(`/order/${orderId}/confirm`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });
                const data = await res.json();
                if (data.success) {
                    setTimeout(() => window.location.reload(), 300);
                } else {
                    alert(data.message);
                }
            } catch(e) {
                alert('Terjadi kesalahan jaringan.');
            }
        },

        async rejectOnlineOrder(orderId, orderCode) {
            const reason = prompt(`Alasan menolak order ${orderCode}? (opsional)`);
            if (reason === null) return; // user cancelled
            
            try {
                const res = await fetch(`/order/${orderId}/reject`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ reason: reason || 'Tidak ada alasan' })
                });
                const data = await res.json();
                if (data.success) {
                    setTimeout(() => window.location.reload(), 300);
                } else {
                    alert(data.message);
                }
            } catch(e) {
                alert('Terjadi kesalahan jaringan.');
            }
        },

        openStatusModal(id, current) {
            this.updateId = id;
            this.currentStatus = current;
            this.statusModalOpen = true;
        },

        async setStatus(newStatus) {
            if (this.currentStatus === newStatus || this.isUpdating) return;
            
            this.currentStatus = newStatus;
            this.isUpdating = true;
            
            try {
                const response = await fetch(`/order/${this.updateId}/status`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ status: newStatus })
                });
                
                const data = await response.json();
                
                if(data.success) {
                    setTimeout(() => {
                        window.location.reload();
                    }, 400);
                } else {
                    alert('Gagal update: ' + data.message);
                    this.isUpdating = false;
                }
            } catch(e) {
                alert('Terjadi kesalahan jaringan.');
                this.isUpdating = false;
            }
        },

        async openPaymentModal(id, current) {
            this.updatePaymentId = id;
            
            if(current === 'Belum Lunas') {
                this.currentPaymentStatus = 'Belum Bayar';
            } else if (current === 'Lunas Cetak') {
                this.currentPaymentStatus = 'Lunas';
            } else {
                this.currentPaymentStatus = current;
            }
            
            this.paymentModalOpen = true;
            this.newPayment = { amount: '', payment_method: 'Cash', note: '' };
            
            // Fetch payment data from API
            try {
                const res = await fetch(`/order/${id}/payments`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                const data = await res.json();
                this.paymentData = data;
            } catch(e) {
                console.error('Failed to fetch payment data', e);
            }
        },

        async submitPayment() {
            if (!this.newPayment.amount || this.newPayment.amount <= 0) {
                alert('Masukkan jumlah pembayaran yang valid.');
                return;
            }
            
            this.isSubmittingPayment = true;
            
            try {
                const res = await fetch(`/order/${this.updatePaymentId}/payments`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(this.newPayment)
                });
                
                const data = await res.json();
                
                if (data.success) {
                    // Update local state
                    this.paymentData.payments.unshift(data.payment);
                    this.paymentData.total_paid = data.total_paid;
                    this.paymentData.remaining = data.remaining;
                    this.currentPaymentStatus = data.payment_status;
                    this.newPayment = { amount: '', payment_method: 'Cash', note: '' };
                    
                    // Reload after short delay to reflect changes
                    setTimeout(() => window.location.reload(), 800);
                } else {
                    alert(data.message || 'Gagal menyimpan pembayaran.');
                }
            } catch(e) {
                alert('Terjadi kesalahan jaringan.');
            } finally {
                this.isSubmittingPayment = false;
            }
        },

        async setPaymentStatus(newStatus) {
            if (this.currentPaymentStatus === newStatus || this.isUpdatingPayment) return;
            
            this.currentPaymentStatus = newStatus;
            this.isUpdatingPayment = true;
            
            try {
                const response = await fetch(`/order/${this.updatePaymentId}/payment-status`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ payment_status: newStatus })
                });
                
                const data = await response.json();
                
                if(data.success) {
                    setTimeout(() => {
                        window.location.reload();
                    }, 400);
                } else {
                    alert('Gagal update: ' + data.message);
                    this.isUpdatingPayment = false;
                }
            } catch(e) {
                alert('Terjadi kesalahan jaringan.');
                this.isUpdatingPayment = false;
            }
        },

        // Photo Documentation Methods
        async openPhotoModal(orderId, orderCode, photoCount) {
            this.photoOrderId = orderId;
            this.photoOrderCode = orderCode;
            this.photoModalOpen = true;
            this.selectedPhotos = [];
            this.photoPreviewUrls = [];
            this.photoType = 'masuk';
            this.photoCaption = '';
            this.orderPhotos = [];
            this.isLoadingPhotos = true;

            try {
                const res = await fetch(`/order/${orderId}/photos`, {
                    headers: { 'Accept': 'application/json' }
                });
                const data = await res.json();
                if (data.success) {
                    this.orderPhotos = data.photos;
                }
            } catch(e) {
                console.error('Failed to load photos', e);
            } finally {
                this.isLoadingPhotos = false;
            }
        },

        handlePhotoSelect(event) {
            const files = Array.from(event.target.files);
            this.addPhotos(files);
            event.target.value = '';
        },

        handlePhotoDrop(event) {
            this.isDragging = false;
            const files = Array.from(event.dataTransfer.files).filter(f => f.type.startsWith('image/'));
            this.addPhotos(files);
        },

        addPhotos(files) {
            const remaining = 5 - this.selectedPhotos.length;
            const toAdd = files.slice(0, remaining);
            toAdd.forEach(file => {
                if (file.size > 5 * 1024 * 1024) {
                    alert(`File ${file.name} terlalu besar. Max 5MB.`);
                    return;
                }
                this.selectedPhotos.push(file);
                this.photoPreviewUrls.push(URL.createObjectURL(file));
            });
        },

        removeSelectedPhoto(index) {
            URL.revokeObjectURL(this.photoPreviewUrls[index]);
            this.selectedPhotos.splice(index, 1);
            this.photoPreviewUrls.splice(index, 1);
        },

        async uploadPhotos() {
            if (this.selectedPhotos.length === 0 || this.isUploadingPhotos) return;
            this.isUploadingPhotos = true;

            const formData = new FormData();
            this.selectedPhotos.forEach(f => formData.append('photos[]', f));
            formData.append('photo_type', this.photoType);
            formData.append('caption', this.photoCaption);

            try {
                const res = await fetch(`/order/${this.photoOrderId}/photos`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: formData
                });
                const data = await res.json();
                if (data.success) {
                    data.photos.forEach(p => this.orderPhotos.unshift(p));
                    this.selectedPhotos = [];
                    this.photoPreviewUrls = [];
                    this.photoCaption = '';
                }
            } catch(e) {
                alert('Gagal upload foto.');
            } finally {
                this.isUploadingPhotos = false;
            }
        },

        async deletePhoto(photoId) {
            if (!confirm('Hapus foto ini?')) return;
            try {
                const res = await fetch(`/order-photos/${photoId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });
                const data = await res.json();
                if (data.success) {
                    this.orderPhotos = this.orderPhotos.filter(p => p.id !== photoId);
                }
            } catch(e) {
                alert('Gagal menghapus foto.');
            }
        }
    }
}
</script>

@endsection
