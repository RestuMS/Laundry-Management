@extends('layouts.dashboard')

@section('title', 'Kasir Dashboard')
@section('header_title', 'Kasir Dashboard')

@section('content')

<!-- Custom CSS Animations for Kasir UI -->
<style>
/* Ripple Effect */
.ripple {
    position: relative;
    overflow: hidden;
    transform: translate3d(0, 0, 0);
}
.ripple::after {
    content: "";
    display: block;
    position: absolute;
    width: 100%;
    height: 100%;
    top: 0;
    left: 0;
    pointer-events: none;
    background-image: radial-gradient(circle, #fff 10%, transparent 10.01%);
    background-repeat: no-repeat;
    background-position: 50%;
    transform: scale(10, 10);
    opacity: 0;
    transition: transform .5s, opacity 1s;
}
.ripple:active::after {
    transform: scale(0, 0);
    opacity: .2;
    transition: 0s;
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

/* Custom Active Highlight row */
.row-active {
    background: linear-gradient(to right, rgba(91,141,239,0.06), rgba(255,255,255,0.4));
    border-left: 3px solid #5B8DEF;
}
</style>

<!-- Top Action Header -->
<div class="flex flex-col lg:flex-row justify-between items-start lg:items-center mb-8 gap-5" x-data="{ loading: true, searchQuery: {!! json_encode(request('search', '')) !!} }" x-init="setTimeout(() => loading = false, 800)">
    
    <!-- Large Search Input -->
    <form action="{{ route('kasir') }}" method="GET" class="relative w-full max-w-3xl glass-card rounded-[20px] focus-within:ring-4 focus-within:ring-primary/20 transition-all shadow-sm group border border-white/80" style="height: 60px;" x-ref="searchForm">
        <template x-if="loading">
            <div class="h-full w-full rounded-[20px] skeleton"></div>
        </template>
        <template x-if="!loading">
            <div class="h-full flex items-center">
                <div class="absolute inset-y-0 left-0 pl-6 flex items-center pointer-events-none text-primary">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
                <input type="text" name="search" x-model="searchQuery" @input.debounce.500ms="$refs.searchForm.submit()" placeholder="Cari trx, nama pelanggan, atau hp..." class="block w-full h-full pl-16 pr-5 bg-transparent border-none focus:ring-0 text-[15px] text-slate-700 placeholder-slate-400 font-medium" />
                <div class="absolute inset-y-0 right-0 pr-5 flex items-center pointer-events-none">
                    <div class="px-2 py-1 text-[11px] font-bold text-slate-400 bg-slate-100 rounded-lg border border-slate-200 shadow-sm group-focus-within:opacity-0 transition-opacity">/</div>
                </div>
            </div>
        </template>
    </form>

    <!-- Main Add Order Button -->
    <div class="w-full lg:w-auto flex-shrink-0" style="height: 60px;">
        <template x-if="loading">
            <div class="h-full w-full lg:w-[220px] rounded-[18px] skeleton"></div>
        </template>
        <template x-if="!loading">
            <a href="{{ route('order.create') }}" class="w-full h-full ripple bg-gradient-to-r from-primary to-[#7FB3FF] hover:from-[#4A7CE0] hover:to-primary text-white px-8 rounded-[18px] flex items-center justify-center gap-3 font-bold text-[15px] shadow-[0_8px_25px_rgba(91,141,239,0.4)] hover:shadow-[0_12px_30px_rgba(91,141,239,0.5)] transform hover:-translate-y-1 transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                Tambah Order
            </a>
        </template>
    </div>
</div>

<!-- Quick Actions Wrapper -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8" x-data="{ loading: true }" x-init="setTimeout(() => loading = false, 1000)">
    
    <!-- Action 1: Scan Barcode -->
    <div style="height: 100px;">
        <template x-if="loading"><div class="h-full w-full rounded-[20px] skeleton"></div></template>
        <template x-if="!loading">
            <button class="w-full h-full ripple glass-card p-5 rounded-[20px] hover-float flex items-center gap-5 text-left group border border-white/60">
                <div class="w-14 h-14 rounded-2xl bg-[#EEF2FF] flex items-center justify-center text-[#6366F1] group-hover:bg-[#6366F1] group-hover:text-white transition-colors duration-300 shadow-sm border border-indigo-100">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm14 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                </div>
                <div>
                    <h3 class="text-[16px] font-bold text-slate-700">Scan Barcode</h3>
                    <p class="text-[13px] font-medium text-slate-500 mt-0.5 group-hover:text-[#6366F1] transition-colors">Cari otomatis via scanner</p>
                </div>
            </button>
        </template>
    </div>

    <!-- Action 2: Pembayaran -->
    <div style="height: 100px;">
        <template x-if="loading"><div class="h-full w-full rounded-[20px] skeleton"></div></template>
        <template x-if="!loading">
            <button class="w-full h-full ripple glass-card p-5 rounded-[20px] hover-float flex items-center gap-5 text-left group border border-white/60">
                <div class="w-14 h-14 rounded-2xl bg-[#ECFDF5] flex items-center justify-center text-[#10B981] group-hover:bg-[#10B981] group-hover:text-white transition-colors duration-300 shadow-sm border border-emerald-100">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                </div>
                <div>
                    <h3 class="text-[16px] font-bold text-slate-700">Pembayaran</h3>
                    <p class="text-[13px] font-medium text-slate-500 mt-0.5 group-hover:text-[#10B981] transition-colors">Checkout pesanan pelanggan</p>
                </div>
            </button>
        </template>
    </div>

    <!-- Action 3: Cetak Struk -->
    <div style="height: 100px;">
        <template x-if="loading"><div class="h-full w-full rounded-[20px] skeleton"></div></template>
        <template x-if="!loading">
            <button class="w-full h-full ripple glass-card p-5 rounded-[20px] hover-float flex items-center gap-5 text-left group border border-white/60">
                <div class="w-14 h-14 rounded-2xl bg-[#FFF7ED] flex items-center justify-center text-[#F97316] group-hover:bg-[#F97316] group-hover:text-white transition-colors duration-300 shadow-sm border border-orange-100">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                </div>
                <div>
                    <h3 class="text-[16px] font-bold text-slate-700">Cetak Struk</h3>
                    <p class="text-[13px] font-medium text-slate-500 mt-0.5 group-hover:text-[#F97316] transition-colors">Reprint resi terakhir</p>
                </div>
            </button>
        </template>
    </div>
</div>

<!-- Order Harian Table -->
<div class="glass-card p-6 md:p-8 rounded-[24px] border border-white/70" x-data="{ loading: true }" x-init="setTimeout(() => loading = false, 1200)">
    
    <!-- Table Header Toolbar -->
    <div class="flex flex-col sm:flex-row items-center justify-between mb-8 gap-4">
        <div>
            <h3 class="text-[18px] font-bold text-slate-700 flex items-center gap-2">
                Order Terkini 
                <span class="flex items-center justify-center min-w-[24px] px-1 h-6 rounded-md bg-[#5B8DEF]/10 text-[#5B8DEF] text-[13px] font-black">{{ $totalOrderHariIni }}</span>
            </h3>
            <p class="text-[13px] text-slate-500 font-medium">Antrean operasional pesanan hari ini</p>
        </div>
        
        <template x-if="!loading">
            <div class="flex gap-2">
                <button class="px-4 py-2.5 rounded-xl text-[13px] font-bold bg-white text-slate-600 border border-slate-200 hover:bg-slate-50 transition-colors shadow-sm focus:outline-none">History</button>
                <button class="px-5 py-2.5 rounded-xl text-[13px] font-bold bg-primary/10 text-primary border border-primary/20 hover:bg-primary/20 transition-colors focus:outline-none flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                    Filter Status
                </button>
            </div>
        </template>
    </div>

    <!-- Skeleton Table Loader -->
    <template x-if="loading">
        <div class="space-y-4">
            <div class="h-12 bg-slate-100/40 rounded-lg"></div>
            <div class="h-20 skeleton rounded-xl"></div>
            <div class="h-20 skeleton rounded-xl"></div>
            <div class="h-20 skeleton rounded-xl"></div>
        </div>
    </template>

    <template x-if="!loading">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse whitespace-nowrap">
                <thead>
                    <tr class="text-[13px] font-semibold text-slate-400 border-b-2 border-slate-100 uppercase tracking-wider">
                        <th class="pb-4 px-4 pl-6">ID / Nama Pelanggan</th>
                        <th class="pb-4 px-4">Layanan</th>
                        <th class="pb-4 px-4">Tanggal Order</th>
                        <th class="pb-4 px-4 text-center">Status</th>
                        <th class="pb-4 px-4 text-right">Total Harga</th>
                        <th class="pb-4 px-4 pr-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-[14.5px] font-semibold text-slate-700">
                    
                    @forelse($orders as $idx => $order)
                    @php
                        // Style variants (just for variation if needed)
                        $isHighPriority = $order->status !== 'Selesai' && $order->status !== 'Diambil';
                        $rowClass = $isHighPriority 
                            ? 'border-b border-slate-100/60 hover:-translate-y-0.5 hover:shadow-[0_10px_20px_rgba(0,0,0,0.03)] transition-all row-active relative overflow-hidden group' 
                            : 'border-b border-slate-100/60 hover:bg-white/40 hover:-translate-y-0.5 hover:shadow-md transition-all group';
                        
                        $colors = ['4F46E5', 'db2777', 'ea580c', '10B981', '3B82F6'];
                        $bgColor = $colors[$order->id % count($colors)];
                        
                        // Status badge logic
                        $badgeStyle = '';
                        if($order->status == 'Diterima' || $order->status == 'Quality Control') {
                            $badgeStyle = 'bg-slate-100 text-slate-600';
                        } elseif($order->status == 'Dicuci' || $order->status == 'Dikeringkan' || $order->status == 'Disetrika' || $order->status == 'Diproses') {
                            $badgeStyle = 'bg-[#FFB84D] text-white shadow-[0_4px_10px_rgba(255,184,77,0.4)]';
                        } elseif($order->status == 'Selesai' || $order->status == 'Diambil') {
                            $badgeStyle = 'bg-[#59C98C] text-white shadow-[0_4px_10px_rgba(89,201,140,0.4)]';
                        } else {
                            $badgeStyle = 'bg-[#FF7A7A] text-white shadow-[0_4px_10px_rgba(255,122,122,0.4)]';
                        }
                    @endphp

                    <tr class="{{ $rowClass }}">
                        <td class="py-4 px-4 pl-6">
                            <div class="flex items-center gap-4">
                                <div class="relative">
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($order->customer_name) }}&background={{ $bgColor }}&color=fff&rounded=true&bold=true" alt="Avatar" class="w-10 h-10 rounded-full shadow-sm ring-2 ring-white">
                                    @if($isHighPriority)
                                    <span class="absolute bottom-0 right-0 w-3 h-3 rounded-full bg-green-400 border-2 border-white"></span>
                                    @endif
                                </div>
                                <div>
                                    <div class="font-bold text-slate-800">{{ $order->customer_name }}</div>
                                    <div class="text-[12px] font-medium text-slate-400 mt-0.5 tracking-wide group-hover:text-primary transition-colors">{{ $order->order_code }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="py-4 px-4 text-slate-600">
                            <div class="flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-[#' . substr(md5($order->items->first()?->service_name ?? $order->service_name), 0, 6) . '] shadow-sm"></span>
                                {{ $order->items->count() > 0 ? $order->items->pluck('service_name')->join(', ') : $order->service_name }} 
                                <span class="text-slate-400 font-medium text-[13px]">({{ $order->items->count() > 0 ? $order->items->sum('qty') . ' Qty' : ($order->weight ? $order->weight . ' Kg/Pcs' : '') }})</span>
                            </div>
                        </td>
                        <td class="py-4 px-4">
                            <div class="text-slate-600 font-medium">{{ $order->created_at->isToday() ? 'Hari ini' : $order->created_at->format('d M y') }}</div>
                            <div class="text-slate-400 font-medium text-[12px]">{{ $order->created_at->format('H:i A') }}</div>
                        </td>
                        <td class="py-4 px-4 text-center">
                            <span class="inline-flex items-center justify-center gap-1.5 px-3 py-1.5 rounded-lg text-[12.5px] font-bold {{ $badgeStyle }} w-full max-w-[110px]">
                                {{ $order->status }}
                            </span>
                        </td>
                        <td class="py-4 px-4 text-right">
                            <div class="font-bold text-[16px] text-slate-700">Rp {{ number_format($order->total_price, 0, ',', '.') }}</div>
                            @if($order->payment_status == 'Lunas')
                            <div class="text-[11.5px] text-emerald-500 font-bold uppercase tracking-wider">LUNAS</div>
                            @else
                            <div class="text-[11.5px] text-orange-400 font-bold uppercase tracking-wider">BELUM LUNAS</div>
                            @endif
                        </td>
                        <td class="py-4 px-4 pr-6 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('order.edit', $order->id) }}" class="p-2.5 rounded-xl bg-slate-50 text-slate-500 hover:text-primary hover:bg-white border border-slate-100 transition-all group-hover:shadow-sm focus:outline-none group-hover:bg-primary/5 group-hover:border-primary/20" title="Edit / Checkout">
                                    <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                </a>
                                <!-- WA Send shortcut -->
                                @php
                                    $phone = $order->customer_phone ? preg_replace('/^08/', '+628', $order->customer_phone) : null;
                                    $msg = "Halo Kak {$order->customer_name}, Transaksi {$order->order_code} statusnya sekarang: {$order->status}. Terima kasih!";
                                    $waLink = $phone ? "https://wa.me/{$phone}?text={$msg}" : "javascript:alert('Belum ada no WA')";
                                @endphp
                                <a href="{{ $waLink }}" target="_blank" class="p-2.5 rounded-xl bg-green-50 text-green-500 hover:text-white hover:bg-green-500 border border-green-100 transition-all group-hover:shadow-sm" title="Kirim WA">
                                    <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-12 text-center text-slate-500 font-medium">Belum ada transaksi di database.</td>
                    </tr>
                    @endforelse

                </tbody>
            </table>
        </div>
        
        <!-- Pagination Wrapper -->
        @if($orders->isNotEmpty())
        <div class="mt-8 flex items-center justify-between px-2 select-none border-t border-slate-100/60 pt-6">
            <span class="text-[13px] font-semibold text-slate-500 bg-white/50 px-3 py-1.5 rounded-lg border border-white/60">Total {{ $orders->total() }} order</span>
            <div class="flex gap-2">
                {{ $orders->links('pagination::tailwind') }}
            </div>
        </div>
        @endif
    </template>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let barcodeBuffer = '';
        let lastKeyTime = Date.now();
        let isProcessing = false;

        window.addEventListener('keydown', function(e) {
            // Ignore if we're in a specific textarea or currently processing
            if (e.target.tagName === 'TEXTAREA' || isProcessing) return;
            
            // If they are focusing on the search bar, it's fine, the buffer will still catch it
            // or we could just capture the search bar input. The beauty of global buffer is that it works anywhere.

            const currentTime = Date.now();
            
            // Scanner acts like a fast typist. If delay between keystrokes > 100ms, it's a human typing.
            if (currentTime - lastKeyTime > 100) {
                barcodeBuffer = '';
            }

            if (e.key === 'Enter' && barcodeBuffer.length > 3) {
                // Potential barcode scanned
                e.preventDefault();
                processBarcodeScan(barcodeBuffer.trim());
                barcodeBuffer = '';
            } else if (e.key !== 'Shift' && e.key !== 'Control' && e.key !== 'Alt') {
                barcodeBuffer += e.key;
            }

            lastKeyTime = currentTime;
        });

        function processBarcodeScan(code) {
            isProcessing = true;
            
            Swal.fire({
                title: 'Barcode Terdeteksi!',
                text: `Memproses order ${code}...`,
                icon: 'info',
                timer: 1000,
                showConfirmButton: false,
                willClose: () => {
                    fetch('{{ route('order.scan') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ order_code: code })
                    })
                    .then(response => response.json())
                    .then(data => {
                        isProcessing = false;
                        if(data.success) {
                            Swal.fire({
                                title: 'Order Selesai!',
                                text: data.message,
                                icon: 'success',
                                confirmButtonColor: '#3B82F6'
                            }).then(() => {
                                window.location.reload();
                            });
                        } else {
                            Swal.fire({
                                title: 'Oops!',
                                text: data.message,
                                icon: 'warning',
                                confirmButtonColor: '#F59E0B'
                            });
                        }
                    })
                    .catch(error => {
                        isProcessing = false;
                        Swal.fire('Error', 'Terjadi kesalahan sistem', 'error');
                        console.error(error);
                    });
                }
            });
        }
    });
</script>
@endpush

@endsection
