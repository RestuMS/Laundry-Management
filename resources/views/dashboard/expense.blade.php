@extends('layouts.dashboard')

@section('title', 'Pencatatan Pengeluaran')
@section('header_title', 'Buku Kas & Pengeluaran Utama')

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

<div x-data="expenseManager()">

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <!-- Add Button Area -->
        <button @click="openModal = true" type="button" class="md:col-span-1 h-[80px] bg-gradient-to-r from-red-500 to-rose-600 hover:from-red-600 hover:to-rose-700 text-white rounded-[24px] flex items-center justify-center gap-3 font-bold text-[18px] shadow-[0_8px_25px_rgba(239,68,68,0.3)] btn-float tracking-wide transition-all w-full">
            <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"></path></svg>
            </div>
            Catat Pengeluaran
        </button>

        <!-- Stats -->
        <div class="md:col-span-2 glass-container rounded-[24px] p-6 shadow-sm flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-red-100/50 rounded-2xl flex items-center justify-center text-red-500 border border-red-200">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <div>
                    <h3 class="text-slate-400 text-[13px] font-bold uppercase tracking-widest mb-1">Total Biaya Filter</h3>
                    <div class="text-3xl font-black text-slate-800 tracking-tight">Rp {{ number_format($totalExpense, 0, ',', '.') }}</div>
                </div>
            </div>
            <div class="text-right">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-slate-100 border border-slate-200 text-slate-500 text-[12px] font-bold">
                    <span class="w-2 h-2 rounded-full bg-blue-400"></span> {{ \Illuminate\Support\Str::limit($filterText, 15) }}
                </span>
            </div>
        </div>
    </div>

    <!-- Table List -->
    <div class="glass-container rounded-[28px] overflow-hidden shadow-[0_8px_30px_rgb(0,0,0,0.04)] mb-8">
        <div class="p-5 md:p-6 border-b border-white/60 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <h3 class="font-bold text-slate-700 text-[17px] md:text-[18px]">Riwayat Pengeluaran Operasional</h3>
            <form action="{{ route('expense.index') }}" method="GET" class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto">
                <div class="relative">
                    <span class="absolute -top-2 left-3 bg-white px-1 text-[10px] font-bold text-slate-400 uppercase tracking-widest rounded">Pilih Hari/Tgl</span>
                    <input type="date" name="date" value="{{ $date_filter ?? '' }}" onchange="this.form.submit()" class="w-full sm:w-[150px] bg-white/50 border border-slate-200 rounded-xl px-3 py-2.5 font-medium text-slate-600 outline-none text-[13px] focus:ring-2 focus:ring-blue-400/30">
                </div>
                @if($date_filter || $month)
                <a href="{{ route('expense.index') }}" class="hidden sm:flex bg-red-50 text-red-500 rounded-xl w-10 h-10 items-center justify-center hover:bg-red-500 hover:text-white transition-colors" title="Reset Kalender">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </a>
                @endif
            </form>
        </div>
        <div class="overflow-x-auto overflow-y-hidden pb-2 w-full no-scrollbar">
            <table class="w-full text-left">
                <thead>
                    <tr class="text-[11px] sm:text-[13.5px] font-bold text-slate-400 uppercase tracking-widest border-b border-white/60">
                        <th class="py-4 md:py-5 px-4 md:px-6 whitespace-nowrap">Tanggal</th>
                        <th class="py-4 md:py-5 px-3 md:px-4 min-w-[150px]">Deskripsi Biaya</th>
                        <th class="py-4 md:py-5 px-3 md:px-4 whitespace-nowrap">Nominal (Rp)</th>
                        <th class="py-4 md:py-5 px-4 md:px-6 text-right whitespace-nowrap">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-[13px] sm:text-[14.5px] font-medium text-slate-600 align-middle">
                    @forelse($expenses as $exp)
                    <tr class="border-b border-white/40 hover:bg-white/50 transition-colors group">
                        <td class="py-3 md:py-4 px-4 md:px-6 text-slate-500 whitespace-nowrap">
                            <span class="sm:hidden">{{ \Carbon\Carbon::parse($exp->date)->translatedFormat('d M y') }}</span>
                            <span class="hidden sm:inline">{{ \Carbon\Carbon::parse($exp->date)->translatedFormat('d F Y') }}</span>
                        </td>
                        <td class="py-3 md:py-4 px-3 md:px-4">
                            <span class="font-bold text-slate-800 {{ strtolower($exp->note) == '' ? '' : 'mb-0.5 md:mb-1' }} block leading-tight">{{ $exp->name }}</span>
                            @if($exp->note)
                                <span class="text-[11px] md:text-[12px] text-slate-400 block break-words whitespace-normal leading-snug"><span class="italic font-normal">"{{ $exp->note }}"</span></span>
                            @endif
                        </td>
                        <td class="py-3 md:py-4 px-3 md:px-4 font-black tracking-tight text-red-500 text-[14px] md:text-[16px] whitespace-nowrap">
                            Rp {{ number_format($exp->amount, 0, ',', '.') }}
                        </td>
                        <td class="py-3 md:py-4 px-4 md:px-6 text-right">
                            <form :action="deleteFormAction" method="POST" x-ref="'deleteForm_' + {{ $exp->id }}" @submit.prevent>
                                @csrf @method('DELETE')
                                <button type="button" @click="confirmDelete('{{ route('expense.destroy', $exp->id) }}')" class="w-8 h-8 md:w-9 md:h-9 rounded-xl bg-red-50 text-red-500 border border-red-100 hover:bg-red-500 hover:text-white transition-all flex items-center justify-center ml-auto">
                                    <svg class="w-4 h-4 md:w-4 md:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="text-center py-10 text-slate-400 text-[13px] md:text-base">Belum ada catatan biaya keluar pada bulan ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($expenses->hasPages())
        <div class="px-6 py-4 border-t border-white/60 text-[14px]">
            {{ $expenses->links('pagination::tailwind') }}
        </div>
        @endif
    </div>

    <!-- Modal Form Create -->
    <div x-show="openModal" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center overflow-hidden w-full h-full" style="display:none;">
        <div x-show="openModal" x-transition.opacity class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm"></div>
        <div x-show="openModal" @click.away="openModal = false" x-transition.scale.95 class="relative bg-white w-full max-w-md mx-4 rounded-[24px] shadow-2xl p-6 z-10 border border-white">
            
            <div class="flex items-center gap-4 mb-6 relative">
                <div class="w-14 h-14 bg-red-50 rounded-full flex items-center justify-center text-red-500 shrink-0">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path></svg>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-slate-800 tracking-tight pt-1">Keluar Kas</h3>
                    <p class="text-[12px] font-medium text-slate-400">Beli Token, Plastik, Gaji, dll</p>
                </div>
            </div>
            
            <form action="{{ route('expense.store') }}" method="POST">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-[13px] font-bold text-slate-500 uppercase tracking-widest mb-2">Nama Pengeluaran</label>
                        <input type="text" name="name" placeholder="cth: Belanja Deterjen Baju" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 font-medium text-slate-700 outline-none focus:ring-2 focus:ring-red-400/30 transition-shadow">
                    </div>
                    
                    <div class="flex gap-4">
                        <div class="flex-1">
                            <label class="block text-[13px] font-bold text-slate-500 uppercase tracking-widest mb-2">Total Harga</label>
                            <div class="relative">
                                <span class="absolute left-4 top-3.5 text-slate-400 font-bold">Rp</span>
                                <input type="number" name="amount" required class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-12 pr-4 py-3 font-bold text-slate-800 outline-none focus:ring-2 focus:ring-red-400/30 transition-shadow">
                            </div>
                        </div>
                        <div class="w-2/5">
                            <label class="block text-[13px] font-bold text-slate-500 uppercase tracking-widest mb-2">Tanggal</label>
                            <input type="date" name="date" value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 font-medium text-slate-700">
                        </div>
                    </div>

                    <div>
                        <label class="block text-[13px] font-bold text-slate-500 uppercase tracking-widest mb-2">Catatan Tambahan (Opsi)</label>
                        <textarea name="note" rows="2" placeholder="Detail pengeluaran..." class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 font-medium text-slate-700 outline-none focus:ring-2 focus:ring-red-400/30 transition-shadow"></textarea>
                    </div>
                </div>

                <div class="mt-8 flex gap-3">
                    <button type="button" @click="openModal = false" class="flex-1 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold py-3.5 rounded-xl transition-colors">Batal</button>
                    <button type="submit" class="flex-1 bg-red-500 hover:bg-red-600 text-white font-bold py-3.5 rounded-xl shadow-[0_4px_15px_rgba(239,68,68,0.3)] transition-transform hover:-translate-y-0.5">Potong Kas</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Konfirmasi Hapus -->
    <div x-show="showDeleteModal" x-cloak class="fixed inset-0 z-[110] flex items-center justify-center overflow-hidden w-full h-full" style="display:none;">
        <div x-show="showDeleteModal" x-transition.opacity class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm"></div>
        <div x-show="showDeleteModal" @click.away="showDeleteModal = false" x-transition.scale.95 class="relative bg-white w-full max-w-sm mx-4 rounded-[24px] shadow-2xl p-6 md:p-8 z-10 border border-white text-center">
            
            <!-- Warning Icon -->
            <div class="mx-auto w-20 h-20 bg-red-50 rounded-full flex items-center justify-center mb-6 relative">
                <div class="absolute inset-0 bg-red-100 rounded-full animate-ping opacity-50"></div>
                <svg class="w-10 h-10 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
            </div>
            
            <h3 class="text-[20px] font-bold text-slate-800 tracking-tight mb-2">Hapus Pengeluaran?</h3>
            <p class="text-[14px] font-medium text-slate-500 mb-8 leading-relaxed">
                Apakah Anda yakin ingin menghapus data pengeluaran ini secara permanen dari sistem?
            </p>
            
            <form :action="deleteFormAction" method="POST" id="confirmDeleteForm">
                @csrf @method('DELETE')
                <div class="flex flex-col gap-3">
                    <button type="submit" class="w-full bg-red-500 hover:bg-red-600 outline-none text-white font-bold py-3.5 px-4 rounded-xl shadow-[0_4px_15px_rgba(239,68,68,0.3)] transition-all hover:-translate-y-0.5 w-full flex items-center justify-center gap-2">
                        <span>Ya, Hapus Data</span>
                    </button>
                    <button type="button" @click="showDeleteModal = false" class="w-full bg-slate-100 hover:bg-slate-200 outline-none text-slate-600 font-bold py-3.5 px-4 rounded-xl transition-colors">
                        Batal
                    </button>
                </div>
            </form>
            
        </div>
    </div>
</div>

<script>
function expenseManager() {
    return {
        openModal: false,
        showDeleteModal: false,
        deleteFormAction: '',
        confirmDelete(actionUrl) {
            this.deleteFormAction = actionUrl;
            this.showDeleteModal = true;
        }
    }
}
</script>
@endsection
