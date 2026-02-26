@extends('layouts.dashboard')

@section('title', 'Edit Order')
@section('header_title', 'Update Data Order')

@section('content')

<style>
/* Glassmorphism Forms */
.glass-form {
    background: rgba(255, 255, 255, 0.75);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.9);
}

.input-cloud {
    background: rgba(255, 255, 255, 0.9);
    border: 1px solid rgba(143, 184, 255, 0.4);
    box-shadow: 0 2px 10px rgba(91, 141, 239, 0.05);
    transition: all 0.3s ease;
}
.input-cloud:focus {
    background: #ffffff;
    box-shadow: 0 0 0 4px rgba(91, 141, 239, 0.15);
    border-color: rgba(91, 141, 239, 0.5);
    outline: none;
}

.btn-primary {
    background: linear-gradient(135deg, #6B9DF2 0%, #5B8DEF 100%);
    box-shadow: 0 4px 15px rgba(107, 157, 242, 0.4);
    transition: all 0.2s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}
.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(107, 157, 242, 0.5);
}

/* Workflow Steps UI */
input[type="radio"]:checked + span {
    color: #4F8EF7;
    font-weight: 800;
}
</style>

<div class="max-w-5xl mx-auto pb-12">
    
    <div class="mb-6 flex justify-between items-center">
        <a href="{{ route('order.index') }}" class="inline-flex items-center gap-2 text-slate-500 hover:text-primary font-semibold transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Kembali
        </a>

        <!-- Form Delete -->
        <form action="{{ route('order.destroy', $order->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus transaksi ini? Data akan dipindahkan ke Recycle Bin (Soft Delete).');">
            @csrf
            @method('DELETE')
            <button type="submit" class="text-red-500 hover:text-red-600 font-bold bg-white/60 hover:bg-white px-5 py-2.5 rounded-xl transition-all border border-red-200 shadow-[0_4px_10px_rgb(0,0,0,0.03)] hover:shadow-md flex items-center gap-2 text-[13px] btn-primary" style="background: white; box-shadow: 0 4px 10px rgba(239, 68, 68, 0.05);">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                Hapus Order
            </button>
        </form>
    </div>

    @if ($errors->any())
        <div class="mb-6 bg-red-50 border border-red-200 text-red-600 px-6 py-4 rounded-2xl shadow-sm">
            <ul class="list-disc pl-5 font-medium text-[14px]">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="glass-form rounded-[28px] p-8 shadow-[0_8px_30px_rgb(0,0,0,0.04)] relative overflow-hidden">
        
        <!-- Decorative Badge -->
        <div class="absolute top-0 right-0 bg-gradient-to-l from-[#4F8EF7] to-[#8FAFFF] text-white px-6 py-3 rounded-bl-3xl font-black text-[15px] shadow-sm flex items-center gap-2">
            # {{ $order->order_code }}
        </div>

        <h2 class="text-2xl font-bold text-slate-700 mb-6 mt-2 flex items-center gap-3">
            <span class="w-8 h-8 rounded-lg bg-[#F0F5FF] text-[#5B8DEF] flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
            </span>
            Update Order & Flow Transaksi
        </h2>

        <form action="{{ route('order.update', $order->id) }}" method="POST" x-data="orderCalculatorEdit()">
            @csrf
            @method('PUT')

            <!-- SECTION 1: PELANGGAN & LAYANAN-->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-6">
                
                <!-- Left Column -->
                <div class="flex flex-col gap-5">
                    
                    <div class="bg-white/50 rounded-2xl p-5 border border-white/60 shadow-sm">
                        <label class="block text-[14px] font-bold text-slate-600 mb-2">Nama Pelanggan <span class="text-red-400">*</span></label>
                        <input type="text" name="customer_name" required value="{{ old('customer_name', $order->customer_name) }}" class="w-full h-12 rounded-[14px] input-cloud px-4 text-[14.5px] font-medium text-slate-700 mb-4">
                        
                        <label class="block text-[14px] font-bold text-slate-600 mb-2">No. Handphone</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none text-slate-400">+62</div>
                            <input type="text" name="customer_phone" value="{{ old('customer_phone', $order->customer_phone) }}" class="w-full h-12 rounded-[14px] input-cloud pl-12 pr-4 text-[14.5px] font-medium text-slate-700">
                        </div>
                    </div>

                    <div class="bg-white/50 rounded-2xl p-5 border border-white/60 shadow-sm">
                        <label class="block text-[14px] font-bold text-slate-600 mb-2">Pilih Layanan <span class="text-red-400">*</span></label>
                        <div class="relative mb-4">
                            <select name="service_name" required class="w-full h-12 rounded-[14px] input-cloud pl-4 pr-10 text-[14.5px] font-medium text-slate-700 appearance-none cursor-pointer">
                                @foreach($services as $svc)
                                    <option value="{{ $svc->service_name }}" data-price="{{ $svc->price }}" {{ old('service_name', $order->service_name) == $svc->service_name ? 'selected' : '' }}>
                                        {{ $svc->service_name }} (Rp {{ number_format($svc->price, 0, ',', '.') }}/{{ $svc->unit }})
                                    </option>
                                @endforeach
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-4 text-slate-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path></svg>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-[14px] font-bold text-slate-600 mb-2">Berat / Qty</label>
                                <input type="number" step="0.1" name="weight" value="{{ old('weight', $order->weight) }}" x-model="weight" @input="calculateEdit()" class="w-full h-12 rounded-[14px] input-cloud px-4 text-[14.5px] font-medium text-slate-700 placeholder-slate-400">
                            </div>
                            <div>
                                <label class="block text-[14px] font-bold text-slate-600 mb-2">Detail Item</label>
                                <input type="text" name="package_detail" value="{{ old('package_detail', $order->package_detail) }}" class="w-full h-12 rounded-[14px] input-cloud px-4 text-[14.5px] font-medium text-slate-700 placeholder-slate-400">
                            </div>
                        </div>

                        <label class="block text-[14px] font-bold text-slate-600 mb-2">Tenggat Waktu Selesai</label>
                        <input type="datetime-local" name="estimated_finish" value="{{ old('estimated_finish', $order->estimated_finish ? \Carbon\Carbon::parse($order->estimated_finish)->format('Y-m-d\TH:i') : '') }}" class="w-full h-12 rounded-[14px] input-cloud px-4 text-[14.5px] font-medium text-slate-500">
                    </div>

                </div>

                <!-- Right Column: Status Laundry -->
                <div class="bg-white/50 rounded-2xl p-6 border border-white/60 shadow-sm flex flex-col h-full">
                    <h3 class="text-lg font-bold text-slate-700 mb-4 border-b border-slate-200/60 pb-3 flex items-center justify-between">
                        <span>Workflow Laundry</span>
                        <div class="w-6 h-6 rounded bg-[#E4F0FF] text-[#4F8EF7] flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2L13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                        </div>
                    </h3>

                    <!-- Status Radio Pills -->
                    <div class="space-y-3">
                        @php
                            $workflows = [
                                'Diterima' => ['icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2', 'color' => '#64748B'],
                                'Dicuci' => ['icon' => 'M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10', 'color' => '#3B82F6'],
                                'Dikeringkan' => ['icon' => 'M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z', 'color' => '#F59E0B'],
                                'Disetrika' => ['icon' => 'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z', 'color' => '#EC4899'],
                                'Quality Control' => ['icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z', 'color' => '#8B5CF6'],
                                'Selesai' => ['icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4', 'color' => '#10B981'],
                                'Diambil' => ['icon' => 'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2z', 'color' => '#14B8A6']
                            ];
                        @endphp
                        
                        @foreach($workflows as $k => $v)
                        <label class="flex items-center gap-4 p-3.5 rounded-2xl border bg-white cursor-pointer hover:shadow-md transition-all group {{ old('status', $order->status) == $k ? 'border-[#4F8EF7] ring-1 ring-[#4F8EF7] shadow-sm' : 'border-slate-200/60' }}">
                            <input type="radio" name="status" value="{{ $k }}" {{ old('status', $order->status) == $k ? 'checked' : '' }} class="w-5 h-5 text-[#4F8EF7] focus:ring-[#4F8EF7] border-slate-300">
                            <span class="flex items-center gap-3 w-full">
                                <span class="w-8 h-8 rounded-[10px] flex items-center justify-center transition-colors group-hover:bg-slate-50" style="color: {{ $v['color'] }}; background: {{ $v['color'] }}15">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="{{ $v['icon'] }}"></path></svg>
                                </span>
                                <span class="text-[14.5px] font-semibold text-slate-600 transition-colors">{{ $k }}</span>
                            </span>
                        </label>
                        @endforeach
                    </div>
                </div>

            </div>

            <hr class="border-slate-200/60 my-6">

            <!-- SECTION 4: PEMBAYARAN & TOTAL -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Payment Config -->
                <div class="bg-white/50 rounded-2xl p-6 border border-white/60 shadow-sm flex flex-col gap-4">
                    <h3 class="text-lg font-bold text-slate-700 mb-2 border-b border-slate-200/60 pb-3">4. Finalisasi Harga</h3>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[13px] font-bold text-slate-600 mb-2">Diskon (Rp)</label>
                            <input type="number" name="discount" value="{{ old('discount', $order->discount) }}" x-model="discount" @input="calculateEdit()" min="0" class="w-full h-11 rounded-[12px] input-cloud px-3 text-[14px] font-medium">
                        </div>
                        <div>
                            <label class="block text-[13px] font-bold text-slate-600 mb-2">Pajak (%)</label>
                            <input type="number" name="tax" value="{{ old('tax', $order->tax) }}" x-model="tax" @input="calculateEdit()" min="0" max="100" class="w-full h-11 rounded-[12px] input-cloud px-3 text-[14px] font-medium">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[13px] font-bold text-slate-600 mb-2">Metode Tipe</label>
                            <div class="relative">
                                <select name="payment_method" class="w-full h-11 rounded-[12px] input-cloud pl-3 pr-8 text-[13.5px] font-medium text-slate-700 appearance-none cursor-pointer">
                                    <option value="Cash" {{ old('payment_method', $order->payment_method) == 'Cash' ? 'selected' : '' }}>Cash / Tunai</option>
                                    <option value="Transfer" {{ old('payment_method', $order->payment_method) == 'Transfer' ? 'selected' : '' }}>Transfer Bank</option>
                                    <option value="E-Wallet" {{ old('payment_method', $order->payment_method) == 'E-Wallet' ? 'selected' : '' }}>E-Wallet (Qris)</option>
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path></svg>
                                </div>
                            </div>
                        </div>
                        <div>
                            <label class="block text-[13px] font-bold text-slate-600 mb-2">Status Bayar</label>
                            <div class="relative">
                                <select name="payment_status" class="w-full h-11 rounded-[12px] input-cloud pl-3 pr-8 text-[13.5px] font-bold text-slate-700 appearance-none cursor-pointer">
                                    <option value="Belum Bayar" {{ old('payment_status', $order->payment_status) == 'Belum Bayar' ? 'selected' : '' }}>Belum Bayar</option>
                                    <option value="DP" {{ old('payment_status', $order->payment_status) == 'DP' ? 'selected' : '' }}>DP (Sebagian)</option>
                                    <option value="Lunas" {{ old('payment_status', $order->payment_status) == 'Lunas' ? 'selected' : '' }}>Lunas</option>
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path></svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Grand Total -->
                <div class="bg-gradient-to-br from-[#5B8DEF] to-[#407BEE] rounded-2xl p-6 shadow-[0_8px_25px_rgba(91,141,239,0.3)] text-white flex flex-col justify-center relative overflow-hidden">
                    <svg class="absolute -bottom-4 -right-4 w-32 h-32 text-white/10" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm.31-8.86c-1.77-.45-2.34-.94-2.34-1.67 0-.84.79-1.43 2.1-1.43 1.38 0 1.9.66 1.94 1.64h1.71c-.05-1.34-.87-2.57-2.49-2.97V5H10.9v1.69c-1.51.32-2.72 1.3-2.72 2.81 0 1.79 1.49 2.69 3.66 3.21 1.95.46 2.34 1.15 2.34 1.87 0 .53-.39 1.64-2.25 1.64-1.74 0-2.1-.96-2.17-1.92H8c.07 1.7 1.3 2.89 2.9 3.21V19h2.36v-1.64c1.84-.37 2.94-1.49 2.94-3.04 0-2.06-1.67-2.73-3.89-3.18z"></path></svg>
                    
                    <p class="text-white/80 font-semibold text-[15px] mb-1">Total Tagihan Estimasi</p>
                    <div class="flex items-end gap-2 mb-4 relative z-10">
                        <span class="text-2xl font-bold">Rp</span>
                        <!-- Allows either script recalculation or db value to show -->
                        <span class="text-5xl font-black tracking-tight" x-text="formatTotal(total)">{{ number_format($order->total_price, 0, ',', '.') }}</span>
                    </div>
                    
                    <div class="space-y-1.5 text-[13px] text-white/80 font-medium relative z-10">
                        <div class="flex justify-between">
                            <span>Subtotal Layanan:</span>
                            <span x-text="'Rp ' + formatTotal(subtotal)">Rp 0</span>
                        </div>
                        <div class="flex justify-between text-red-100 border-t border-white/20 pt-1 mt-1">
                            <span>Status Terkini:</span>
                            <span>{{ $order->payment_status }}</span>
                        </div>
                    </div>
                    
                    <!-- Hidden input to post the final parsed JS calculated amount -->
                    <input type="hidden" name="total_price" :value="total">
                </div>
            </div>

            <!-- Catatan -->
            <div class="mt-6 bg-white/50 rounded-2xl p-6 border border-white/60 shadow-sm relative z-20">
                <label class="block text-[14px] font-bold text-slate-600 mb-2">Catatan Historis & Tambahan</label>
                <textarea name="notes" placeholder="Berikan info tambahan atau riwayat kerusakan.." class="w-full h-20 rounded-[14px] input-cloud p-4 text-[14.5px] font-medium text-slate-700 placeholder-slate-400 align-top resize-none">{{ old('notes', $order->notes) }}</textarea>
            </div>

            <div class="flex justify-end pt-8 mt-4 border-t border-slate-200/60 font-bold">
                <button type="submit" class="h-14 btn-primary text-white px-10 rounded-[18px] text-[16px] flex items-center gap-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                    Update Semua Perubahan
                </button>
            </div>
            
        </form>
    </div>
</div>

<script>
function orderCalculatorEdit() {
    return {
        // Init state from server value, but weight may change
        weight: '{{ old('weight', $order->weight) }}',
        discount: '{{ old('discount', $order->discount) }}',
        tax: '{{ old('tax', $order->tax) }}',
        
        get subtotal() {
            let w = parseFloat(this.weight) || 0;
            const selectEl = document.querySelector('select[name="service_name"]');
            if(!selectEl) return 0;
            const option = selectEl.options[selectEl.selectedIndex];
            const priceBase = parseFloat(option.getAttribute('data-price') || 0);
            return priceBase * w;
        },

        get taxAmount() {
            let taxPerc = parseFloat(this.tax) || 0;
            let sub = this.subtotal - (parseFloat(this.discount) || 0);
            if(sub < 0) sub = 0;
            return Math.round(sub * (taxPerc / 100));
        },

        get total() {
            if(!this.weight) return parseInt('{{ $order->total_price }}') || 0;
            
            let disc = parseFloat(this.discount) || 0;
            let calc = this.subtotal - disc + this.taxAmount;
            return calc > 0 ? calc : 0;
        },

        formatTotal(num) {
            return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        },

        calculateEdit() {
            // Keep state bound
        }
    }
}
</script>

@endsection
