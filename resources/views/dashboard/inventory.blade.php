@extends('layouts.dashboard')

@section('title', 'Kelola Bahan Baku')
@section('header_title', 'Stok Bahan Baku & Inventory')

@section('content')
<style>
.glass-container {
    background: rgba(255, 255, 255, 0.75);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.9);
}
.btn-float:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px -3px rgba(91, 141, 239, 0.3);
}
</style>

<div x-data="inventoryManager()">

    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
        <form action="{{ route('inventory.index') }}" method="GET" class="flex-1 w-full" x-ref="searchForm">
            <div class="w-full bg-white/70 backdrop-blur-md border border-white/80 rounded-[20px] shadow-[0_4px_20px_rgb(0,0,0,0.03)] flex items-center h-[56px] px-5 focus-within:ring-4 focus-within:ring-blue-100 transition-all">
                <svg class="w-5 h-5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Cari bahan baku (contoh: Deterjen, Parfum)..." class="w-full h-full bg-transparent border-none focus:ring-0 text-[15px] text-slate-600 placeholder-slate-400 font-medium px-4" />
                @if(request('search'))
                <a href="{{ route('inventory.index') }}" class="w-6 h-6 rounded-full bg-slate-200 flex items-center justify-center text-slate-500 hover:bg-slate-300"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg></a>
                @endif
            </div>
        </form>

        <button @click="openCreateModal" type="button" class="shrink-0 h-[56px] bg-gradient-to-r from-[#4F8EF7] to-[#1E6DEB] hover:from-[#3a7ae6] hover:to-[#0f5bdd] text-white px-8 rounded-[20px] flex items-center gap-3 font-bold text-[15px] shadow-[0_4px_20px_rgba(79,142,247,0.4)] btn-float tracking-wide transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3.5" d="M12 4v16m8-8H4"></path></svg>
            Tambah Bahan
        </button>
    </div>

    <!-- Alert for Low Stock -->
    @php
        $lowStocks = $inventories->filter(function($inv) {
            return $inv->stock <= $inv->minimum_stock;
        });
    @endphp

    @if($lowStocks->count() > 0)
    <div class="mb-6 bg-red-50 border border-red-200 rounded-[20px] p-5 flex gap-4 animate-pulse-slow relative overflow-hidden">
        <div class="absolute inset-0 bg-red-400 opacity-10 animate-pulse"></div>
        <div class="w-12 h-12 bg-red-100 text-red-500 rounded-full flex items-center justify-center shrink-0 shadow-inner relative z-10">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
        </div>
        <div class="relative z-10">
            <h3 class="font-bold text-red-700 text-[16px]">Peringatan! Stok Hampir Habis</h3>
            <p class="text-[14px] text-red-600 font-medium">Beberapa bahan baku berada di bawah batas minimum: 
                @foreach($lowStocks as $low)
                    <strong class="font-bold border-b border-red-300 border-dashed">{{ $low->name }} (Sisa {{ $low->stock }} {{ $low->unit }})</strong>{{ !$loop->last ? ', ' : '' }}
                @endforeach
            </p>
        </div>
    </div>
    @endif

    <div class="glass-container rounded-[28px] overflow-hidden shadow-[0_8px_30px_rgb(0,0,0,0.04)] mb-8">
        <div class="overflow-x-auto pb-2">
            <table class="w-full text-left whitespace-nowrap min-w-[800px]">
                <thead>
                    <tr class="text-[13.5px] font-bold text-slate-400 uppercase tracking-widest border-b border-white/60">
                        <th class="py-5 px-6">Bahan Baku</th>
                        <th class="py-5 px-4">Sisa Stok</th>
                        <th class="py-5 px-4 hidden md:table-cell">Usage / Kg Cucian</th>
                        <th class="py-5 px-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-[14.5px] font-medium text-slate-600 align-middle">
                    @forelse($inventories as $inv)
                    <tr class="border-b border-white/40 hover:bg-white/50 transition-colors group">
                        <td class="py-4 px-6">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-xl bg-orange-50 text-orange-500 flex items-center justify-center border border-orange-100 shrink-0">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
                                </div>
                                <div>
                                    <div class="font-bold text-slate-800 text-[15px] block">{{ $inv->name }}</div>
                                    <div class="text-[12px] text-slate-400">Min. Stok: {{ $inv->minimum_stock }} {{ $inv->unit }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="py-4 px-4 font-bold text-[16px]">
                            @if($inv->stock <= $inv->minimum_stock)
                                <span class="bg-red-100 text-red-600 px-3 py-1 rounded-lg border border-red-200">{{ $inv->stock }} {{ $inv->unit }}</span>
                            @else
                                <span class="text-slate-700">{{ $inv->stock }} <span class="text-slate-400 font-medium text-[13px]">{{ $inv->unit }}</span></span>
                            @endif
                        </td>
                        <td class="py-4 px-4 text-slate-500 hidden md:table-cell">
                            - {{ $inv->usage_per_kg }} {{ $inv->unit }} <span class="text-[12px] opacity-70">tiap transaksi cuci</span>
                        </td>
                        <td class="py-4 px-6 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <button type="button" @click="openEditModal({{ $inv }})" class="w-9 h-9 rounded-xl bg-blue-50 text-blue-500 border border-blue-100 hover:bg-blue-500 hover:text-white transition-all flex items-center justify-center">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                </button>
                                <form action="{{ route('inventory.destroy', $inv->id) }}" method="POST" onsubmit="return confirm('Hapus bahan baku ini?');" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="w-9 h-9 rounded-xl bg-red-50 text-red-500 border border-red-100 hover:bg-red-500 hover:text-white transition-all flex items-center justify-center">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="text-center py-10 text-slate-400">Tidak ada data bahan baku. Tambahkan sekarang.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($inventories->hasPages())
        <div class="px-6 py-4 border-t border-white/60">
            {{ $inventories->links('pagination::tailwind') }}
        </div>
        @endif
    </div>

    <!-- Modal Form (Create/Edit) -->
    <div x-show="modalOpen" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center overflow-hidden w-full h-full" style="display:none;">
        <div x-show="modalOpen" x-transition.opacity class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm"></div>
        <div x-show="modalOpen" @click.away="modalOpen = false" x-transition.scale.95 class="relative bg-white w-full max-w-md mx-4 rounded-[24px] shadow-2xl p-6 z-10 border border-white">
            <h3 class="text-2xl font-bold text-slate-800 mb-6 tracking-tight" x-text="isEdit ? 'Update Bahan Baku' : 'Tambah Bahan Baku'"></h3>
            
            <form :action="formUrl" method="POST">
                @csrf
                <input type="hidden" name="_method" :value="isEdit ? 'PUT' : 'POST'">
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-[13px] font-bold text-slate-500 uppercase tracking-widest mb-2">Nama Bahan</label>
                        <input type="text" name="name" x-model="formData.name" placeholder="cth: Deterjen Cair Biru" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 font-medium text-slate-700 focus:ring-2 focus:ring-[#4F8EF7]/40 outline-none transition-all">
                    </div>
                    
                    <div class="flex gap-4">
                        <div class="flex-1">
                            <label class="block text-[13px] font-bold text-slate-500 uppercase tracking-widest mb-2">Stok Saat Ini</label>
                            <input type="number" step="0.01" name="stock" x-model="formData.stock" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 font-medium text-slate-700">
                        </div>
                        <div class="w-1/3">
                            <label class="block text-[13px] font-bold text-slate-500 uppercase tracking-widest mb-2">Satuan</label>
                            <select name="unit" x-model="formData.unit" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 font-medium text-slate-700">
                                <option value="ml">ml (Mili)</option>
                                <option value="pcs">Pcs / Lembar</option>
                                <option value="gram">Gram</option>
                            </select>
                        </div>
                    </div>

                    <div class="flex gap-4">
                        <div class="flex-1">
                            <label class="block text-[13px] font-bold text-slate-500 uppercase tracking-widest mb-2">Potongan / Kg Order</label>
                            <input type="number" step="0.01" name="usage_per_kg" x-model="formData.usage_per_kg" placeholder="cth: 50" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 font-medium text-slate-700">
                            <p class="text-[11px] text-slate-400 mt-1">*Bahan otomatis terpotong saat kasir input cucian pelanggan (Isi 0 jika tidak ada)</p>
                        </div>
                        <div class="flex-1">
                            <label class="block text-[13px] font-bold justify-between text-slate-500 uppercase tracking-widest mb-2 flex">Stok Minimum</label>
                            <input type="number" step="0.01" name="minimum_stock" x-model="formData.minimum_stock" required class="w-full border-red-200 bg-red-50 border rounded-xl px-4 py-3 font-bold text-red-600">
                        </div>
                    </div>
                </div>

                <div class="mt-8 flex gap-3">
                    <button type="button" @click="modalOpen = false" class="flex-1 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold py-3.5 rounded-xl transition-colors">Batal</button>
                    <button type="submit" class="flex-1 bg-[#4F8EF7] hover:bg-blue-600 text-white font-bold py-3.5 rounded-xl shadow-lg transition-transform hover:-translate-y-0.5">Simpan Data</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function inventoryManager() {
    return {
        modalOpen: false,
        isEdit: false,
        formUrl: '{{ route('inventory.store') }}',
        formData: { name: '', stock: 0, unit: 'ml', usage_per_kg: 0, minimum_stock: 0 },
        
        openCreateModal() {
            this.isEdit = false;
            this.formUrl = '{{ route('inventory.store') }}';
            this.formData = { name: '', stock: 0, unit: 'ml', usage_per_kg: 0, minimum_stock: 0 };
            this.modalOpen = true;
        },
        openEditModal(item) {
            this.isEdit = true;
            this.formUrl = '{{ url('inventaris') }}/' + item.id;
            this.formData = { ...item };
            this.modalOpen = true;
        }
    }
}
</script>
@endsection
