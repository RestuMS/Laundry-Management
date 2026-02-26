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

<div x-data="orderManager()">
    
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

    <!-- Table Section -->
    <div class="glass-container rounded-[28px] p-2 overflow-hidden shadow-[0_8px_30px_rgb(0,0,0,0.04)] mb-8">
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
                                    <span class="text-[12.5px] font-bold text-[#4F8EF7] bg-[#F0F5FF] px-2 py-0.5 rounded-md inline-block">
                                        {{ $order->order_code }}
                                    </span>
                                </div>
                            </div>
                        </td>
                        
                        <td class="py-5 px-4">
                            <div class="font-bold text-slate-700 block mb-1">{{ $order->service_name }}</div>
                            <div class="text-[13px] text-slate-400 flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"></path></svg>
                                {{ $order->weight ? $order->weight . ' Kg/Pcs' : 'Tidak ada berat' }}
                            </div>
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
                                if($order->status == 'Diterima' || $order->status == 'Quality Control') {
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
                                <span class="text-[12px] font-bold text-red-400 inline-flex items-center gap-1"><span class="w-1.5 h-1.5 rounded-full bg-red-400"></span> Belum Bayar</span>
                            @elseif($order->payment_status == 'DP')
                                <span class="text-[12px] font-bold text-orange-400 inline-flex items-center gap-1"><span class="w-1.5 h-1.5 rounded-full bg-orange-400"></span> DP Sebagian</span>
                            @else
                                <span class="text-[12px] font-bold text-[#10B981] inline-flex items-center gap-1"><span class="w-1.5 h-1.5 rounded-full bg-[#10B981]"></span> Lunas Cetak</span>
                            @endif
                        </td>

                        <td class="py-5 px-4 font-black text-slate-700 text-[15px]">
                            Rp {{ number_format($order->total_price, 0, ',', '.') }}
                        </td>

                        <td class="py-5 px-6 text-right {{ $loop->last ? 'rounded-br-[20px]' : '' }}">
                            <div class="flex items-center justify-end gap-2">
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

                                <!-- Delete -->
                                @if(Auth::user()->role === 'admin')
                                <button type="button" @click="confirmDelete('{{ $order->id }}', '{{ $order->order_code }}', '{{ addslashes($order->customer_name) }}')" class="w-10 h-10 rounded-xl bg-red-50 border border-red-200 text-red-500 hover:bg-red-500 hover:text-white transition-all flex items-center justify-center shadow-sm">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
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

        <!-- Pagination -->
        @if($orders->hasPages())
        <div class="px-6 py-4 border-t border-white/60">
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

</div>

<script>
function orderManager() {
    return {
        searchQuery: '',
        deleteModalOpen: false,
        deleteId: '',
        deleteCode: '',
        deleteName: '',
        deleteUrl: '',
        
        statusModalOpen: false,
        updateId: '',
        currentStatus: '',
        isUpdating: false,
        
        statuses: [
            { name: 'Diterima', icon: 'M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4', bgActive: 'bg-slate-100', textActive: 'text-slate-700' },
            { name: 'Dicuci', icon: 'M5 4h14a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2z M12 17a5 5 0 1 0 0-10 5 5 0 0 0 0 10z M12 15a3 3 0 1 0 0-6 3 3 0 0 0 0 6z M7 8h2', bgActive: 'bg-blue-100', textActive: 'text-blue-600' },
            { name: 'Dikeringkan', icon: 'M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z', bgActive: 'bg-amber-100', textActive: 'text-amber-600' },
            { name: 'Disetrika', icon: 'M4 17h14c1.5 0 3-1.5 3-3 0-3-3-5-6-5H4v8z M4 9V6c0-1.5 1.5-3 3-3h5c1.5 0 3 1.5 3 3v3', bgActive: 'bg-pink-100', textActive: 'text-pink-600' },
            { name: 'Quality Control', icon: 'M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z', bgActive: 'bg-indigo-100', textActive: 'text-indigo-600' },
            { name: 'Selesai', icon: 'M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z', bgActive: 'bg-emerald-100', textActive: 'text-emerald-600' },
            { name: 'Diambil', icon: 'M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z M9 15l2 2 4-4', bgActive: 'bg-teal-100', textActive: 'text-teal-600' }
        ],

        submitSearch() {
            this.$refs.searchForm.submit();
        },

        confirmDelete(id, code, name) {
            this.deleteId = id;
            this.deleteCode = code;
            this.deleteName = name;
            // Built dynamic URL route inside logic
            const baseUrl = "{{ url('order') }}";
            this.deleteUrl = `${baseUrl}/${id}`;
            this.deleteModalOpen = true;
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
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ status: newStatus })
                });
                
                const data = await response.json();
                
                if(data.success) {
                    // Slight delay for UI visual feedback before reload
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
        }
    }
}
</script>

@endsection
