@extends('layouts.dashboard')

@section('title', 'Tambah Order')
@section('header_title', 'Form Order Baru')

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
</style>

<div class="max-w-5xl mx-auto pb-12" x-data="orderCalculator()">
    
    <div class="mb-6">
        <a href="{{ route('order.index') }}" class="inline-flex items-center gap-2 text-slate-500 hover:text-primary font-semibold transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Kembali ke Daftar Order
        </a>
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

    <div class="glass-form rounded-[28px] p-8 shadow-[0_8px_30px_rgb(0,0,0,0.04)]">
        <h2 class="text-2xl font-bold text-slate-700 mb-6 flex items-center gap-3">
            <span class="w-8 h-8 rounded-lg bg-[#F0F5FF] text-[#5B8DEF] flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
            </span>
            Informasi Transaksi Baru
        </h2>

        <form action="{{ route('order.store') }}" method="POST">
            @csrf

            <!-- SECTION 1: PELANGGAN -->
            <div class="bg-white/50 rounded-2xl p-6 border border-white/60 shadow-sm mb-6" x-data="customerAutocomplete()">
                <h3 class="text-lg font-bold text-slate-700 mb-5 border-b border-slate-200/60 pb-3">1. Data Pelanggan</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 relative">
                    <!-- Autocomplete Wrapper -->
                    <div class="relative">
                        <label class="block text-[14px] font-bold text-slate-600 mb-2">Nama Pelanggan <span class="text-red-400">*</span></label>
                        <input type="text" name="customer_name" required x-model="search" @input="filterCustomers" @focus="showDropdown = true" @click.away="showDropdown = false" placeholder="Ketik nama pelanggan..." class="w-full h-12 rounded-[14px] input-cloud px-4 text-[14.5px] font-medium text-slate-700 placeholder-slate-400" autocomplete="off">
                        
                        <!-- Dropdown Options -->
                        <div x-show="showDropdown && filteredCustomers.length > 0" x-cloak class="absolute z-50 w-full mt-1 bg-white border border-slate-200 shadow-xl rounded-xl max-h-60 overflow-y-auto custom-scrollbar">
                            <template x-for="cust in filteredCustomers" :key="cust.customer_name + cust.customer_phone">
                                <div @click="selectCustomer(cust)" class="px-4 py-3 hover:bg-slate-50 cursor-pointer border-b border-slate-100 last:border-0 transition-colors">
                                    <div class="font-bold text-slate-700 text-[14px]" x-text="cust.customer_name"></div>
                                    <div class="text-[12px] text-slate-400 font-medium" x-text="cust.customer_phone ? '+62' + cust.customer_phone.toString().replace(/^0+/, '') : 'Tanpa No HP'"></div>
                                </div>
                            </template>
                        </div>
                    </div>
                    <div>
                        <label class="block text-[14px] font-bold text-slate-600 mb-2">No. Handphone</label>
                        <div class="relative flex items-center">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none text-slate-400 z-10">+62</div>
                            <input type="text" name="customer_phone" x-model="phone" placeholder="8123xxxx" class="w-full h-12 rounded-[14px] input-cloud pl-12 pr-4 text-[14.5px] font-medium text-slate-700 placeholder-slate-400">
                        </div>
                    </div>
                </div>
            </div>

            <!-- SECTION 2: KERANJANG LAYANAN (MULTIPLE ITEMS) -->
            <div class="bg-white/50 rounded-2xl p-6 border border-white/60 shadow-sm mb-6">
                <div class="flex justify-between items-center mb-5 border-b border-slate-200/60 pb-3">
                    <h3 class="text-lg font-bold text-slate-700">2. Keranjang Layanan</h3>
                    <button type="button" @click="addItem()" class="px-4 py-2 bg-[#F0F5FF] text-[#5B8DEF] text-[13px] font-bold rounded-xl hover:bg-[#5B8DEF] hover:text-white transition-all flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                        Tambah Item
                    </button>
                </div>
                
                <div class="space-y-4 mb-6">
                    <template x-for="(item, index) in items" :key="index">
                        <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end bg-slate-50 border border-slate-100 p-4 rounded-2xl relative group">
                            <!-- Remove Button -->
                            <button type="button" @click="removeItem(index)" x-show="items.length > 1" class="absolute -top-2 -right-2 w-7 h-7 bg-red-100 text-red-500 rounded-full flex items-center justify-center shadow-sm opacity-0 group-hover:opacity-100 transition-opacity">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>

                            <!-- Service Choice -->
                            <div class="md:col-span-5">
                                <label class="block text-[13px] font-bold text-slate-600 mb-2">Pilih Layanan <span class="text-red-400">*</span></label>
                                <select x-model="item.service_id" @change="onServiceChange(index)" :name="'items['+index+'][service_name]'" required class="w-full h-11 rounded-[12px] input-cloud px-3 text-[13.5px] font-medium text-slate-700">
                                    <option value="">-- Pilih --</option>
                                    @foreach($services as $svc)
                                        <option value="{{ $svc->service_name }}" data-price="{{ $svc->price }}" data-unit="{{ $svc->unit }}">
                                            {{ $svc->service_name }} (Rp {{ number_format($svc->price, 0, ',', '.') }}/{{ $svc->unit }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Qty/Weight -->
                            <div class="md:col-span-3">
                                <label class="block text-[13px] font-bold text-slate-600 mb-2">Qty / Berat <span class="text-red-400">*</span></label>
                                <div class="relative">
                                    <input type="number" step="0.01" x-model="item.qty" @input="calculate()" :name="'items['+index+'][qty]'" required placeholder="1" class="w-full h-11 rounded-[12px] input-cloud pl-3 pr-12 text-[13.5px] font-medium text-slate-700">
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-slate-400 font-bold text-[12px]" x-text="item.unit || '-'">
                                    </div>
                                    <input type="hidden" :name="'items['+index+'][unit]'" :value="item.unit">
                                </div>
                            </div>

                            <!-- Subtotal display -->
                            <div class="md:col-span-4 pl-2">
                                <label class="block text-[12px] font-bold text-slate-400 mb-1">Subtotal</label>
                                <div class="text-[15px] font-black text-slate-700">Rp <span x-text="formatTotal(item.subtotal)">0</span></div>
                                <input type="hidden" :name="'items['+index+'][price]'" :value="item.price">
                                <input type="hidden" :name="'items['+index+'][subtotal]'" :value="item.subtotal">
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Est Finish (Global for Order) -->
                <div class="mb-5">
                    <label class="block text-[14px] font-bold text-slate-600 mb-2">Estimasi Selesai Keseluruhan <span class="text-red-400">*</span></label>
                    <input type="datetime-local" name="estimated_finish" x-model="estFinish" required class="w-full h-12 rounded-[14px] input-cloud px-4 text-[14.5px] font-medium text-slate-700 sm:w-1/2">
                </div>

                <div class="w-full">
                    <label class="block text-[14px] font-bold text-slate-600 mb-2">Detail Paket / Catatan (Opsional)</label>
                    <textarea name="notes" placeholder="Contoh: 2 Kemeja putih, 1 celana jeans. Tolong pisahkan luntur..." class="w-full h-20 rounded-[14px] input-cloud p-4 text-[14.5px] font-medium text-slate-700 placeholder-slate-400 align-top resize-none"></textarea>
                </div>
            </div>

            <!-- SECTION 3: PEMBAYARAN & TOTAL -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                
                <!-- Payment Config -->
                <div class="bg-white/50 rounded-2xl p-6 border border-white/60 shadow-sm flex flex-col gap-4">
                    <h3 class="text-lg font-bold text-slate-700 mb-2 border-b border-slate-200/60 pb-3">3. Pembayaran</h3>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[13px] font-bold text-slate-600 mb-2">Diskon (Rp)</label>
                            <input type="number" name="discount" x-model="discount" @input="calculate()" value="0" min="0" class="w-full h-11 rounded-[12px] input-cloud px-3 text-[14px] font-medium">
                        </div>
                        <div>
                            <label class="block text-[13px] font-bold text-slate-600 mb-2">Pajak (%)</label>
                            <input type="number" name="tax" x-model="tax" @input="calculate()" value="0" min="0" max="100" class="w-full h-11 rounded-[12px] input-cloud px-3 text-[14px] font-medium">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[13px] font-bold text-slate-600 mb-2">Metode</label>
                            <div class="relative">
                                <select name="payment_method" class="w-full h-11 rounded-[12px] input-cloud pl-3 pr-8 text-[13.5px] font-medium text-slate-700 appearance-none cursor-pointer">
                                    <option value="Cash">Cash / Tunai</option>
                                    <option value="Transfer">Transfer Bank</option>
                                    <option value="E-Wallet">E-Wallet (Qris)</option>
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path></svg>
                                </div>
                            </div>
                        </div>
                        <div>
                            <label class="block text-[13px] font-bold text-slate-600 mb-2">Status Bayar</label>
                            <div class="relative">
                                <select name="payment_status" class="w-full h-11 rounded-[12px] input-cloud pl-3 pr-8 text-[13.5px] font-medium text-slate-700 appearance-none cursor-pointer">
                                    <option value="Belum Bayar">Belum Bayar</option>
                                    <option value="DP">DP (Sebagian)</option>
                                    <option value="Lunas">Lunas</option>
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
                    <!-- Decor -->
                    <svg class="absolute -bottom-4 -right-4 w-32 h-32 text-white/10" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm.31-8.86c-1.77-.45-2.34-.94-2.34-1.67 0-.84.79-1.43 2.1-1.43 1.38 0 1.9.66 1.94 1.64h1.71c-.05-1.34-.87-2.57-2.49-2.97V5H10.9v1.69c-1.51.32-2.72 1.3-2.72 2.81 0 1.79 1.49 2.69 3.66 3.21 1.95.46 2.34 1.15 2.34 1.87 0 .53-.39 1.64-2.25 1.64-1.74 0-2.1-.96-2.17-1.92H8c.07 1.7 1.3 2.89 2.9 3.21V19h2.36v-1.64c1.84-.37 2.94-1.49 2.94-3.04 0-2.06-1.67-2.73-3.89-3.18z"></path></svg>
                    
                    <p class="text-white/80 font-semibold text-[15px] mb-1">Total Tagihan Estimasi</p>
                    <div class="flex items-end gap-2 mb-4 relative z-10">
                        <span class="text-2xl font-bold">Rp</span>
                        <span class="text-5xl font-black tracking-tight" x-text="formatTotal(total)">0</span>
                    </div>
                    
                    <div class="space-y-1.5 text-[13px] text-white/80 font-medium relative z-10">
                        <div class="flex justify-between">
                            <span>Subtotal Layanan:</span>
                            <span x-text="'Rp ' + formatTotal(subtotal)">Rp 0</span>
                        </div>
                        <div class="flex justify-between text-red-100">
                            <span>Diskon (Potongan):</span>
                            <span x-text="'- Rp ' + formatTotal(discount)">- Rp 0</span>
                        </div>
                        <div class="flex justify-between text-green-100">
                            <span>Pajak Tambahan:</span>
                            <span x-text="'+ Rp ' + formatTotal(taxAmount)">+ Rp 0</span>
                        </div>
                    </div>

                    <!-- Hidden actual input to post to server -->
                    <input type="hidden" name="total_price" :value="total">
                </div>

            </div>

            <div class="flex justify-end pt-8 mt-4">
                <button type="submit" class="h-14 btn-primary text-white px-10 rounded-[16px] font-bold text-[16px] flex items-center gap-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                    Simpan Order Sekarang
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function customerAutocomplete() {
    return {
        customers: @json($customers->unique('customer_name')->values()->all() ?? []),
        search: '{{ old('customer_name') }}',
        phone: '{{ old('customer_phone') }}',
        showDropdown: false,
        
        get filteredCustomers() {
            if (this.search === '') return [];
            return this.customers.filter(c => c.customer_name.toLowerCase().includes(this.search.toLowerCase())).slice(0, 10);
        },
        
        filterCustomers() {
            this.showDropdown = true;
        },
        
        selectCustomer(cust) {
            this.search = cust.customer_name;
            let cleanPhone = cust.customer_phone ? cust.customer_phone.toString().replace(/^0+/, '') : '';
            if (cleanPhone.startsWith('62')) cleanPhone = cleanPhone.substring(2);
            this.phone = cleanPhone;
            this.showDropdown = false;
        }
    }
}

function orderCalculator() {
    return {
        items: [{ service_id: '', qty: 1, unit: '', price: 0, subtotal: 0 }],
        discount: 0,
        tax: 0,
        estFinish: '',
        
        addItem() {
            this.items.push({ service_id: '', qty: 1, unit: '', price: 0, subtotal: 0 });
            this.calculate();
        },
        
        removeItem(index) {
            this.items.splice(index, 1);
            this.calculate();
        },
        
        onServiceChange(index) {
            const el = document.querySelector(`select[name="items[${index}][service_name]"]`);
            if(el && el.selectedIndex > 0) {
                const opt = el.options[el.selectedIndex];
                this.items[index].price = parseFloat(opt.getAttribute('data-price') || 0);
                this.items[index].unit = opt.getAttribute('data-unit') || 'Pcs';
                
                // Auto estimate on first item
                if (index === 0 && !this.estFinish) {
                    let svcName = opt.value || '';
                    let d = new Date();
                    if(svcName.includes('Satuan') || svcName.includes('Sepatu') || svcName.includes('Bed Cover')) {
                        d.setDate(d.getDate() + 3);
                    } else {
                        d.setDate(d.getDate() + 2);
                    }
                    const offset = d.getTimezoneOffset();
                    const adjustedDate = new Date(d.getTime() - (offset*60*1000));
                    this.estFinish = adjustedDate.toISOString().slice(0,16);
                }
            } else {
                this.items[index].price = 0;
                this.items[index].unit = '';
            }
            this.calculate();
        },
        
        get subtotal() {
            return this.items.reduce((sum, item) => sum + (parseFloat(item.subtotal) || 0), 0);
        },

        get taxAmount() {
            let taxPerc = parseFloat(this.tax) || 0;
            let sub = this.subtotal - (parseFloat(this.discount) || 0);
            if(sub < 0) sub = 0;
            return Math.round(sub * (taxPerc / 100));
        },

        get total() {
            let disc = parseFloat(this.discount) || 0;
            let calc = this.subtotal - disc + this.taxAmount;
            return calc > 0 ? calc : 0;
        },

        formatTotal(num) {
            return num ? num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".") : "0";
        },

        calculate() {
            this.items.forEach(item => {
                let p = parseFloat(item.price) || 0;
                let q = parseFloat(item.qty) || 0;
                item.subtotal = p * q;
            });
        },

        init() {
            setTimeout(() => this.calculate(), 100);
        }
    }
}
</script>

@endsection
