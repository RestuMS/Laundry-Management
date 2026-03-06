@extends('layouts.dashboard')

@section('title', 'Keluhan Pelanggan')
@section('header_title', 'Tiket Keluhan Pelanggan')

@section('content')

<style>
/* Smooth Floating & Animations */
.glass-container {
    background: rgba(255, 255, 255, 0.75);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.9);
}

.custom-scrollbar::-webkit-scrollbar { height: 6px; width: 6px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(143, 184, 255, 0.4); border-radius: 10px; }
.custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #5B8DEF; }

[x-cloak] { display: none !important; }
</style>

<div x-data="{
    resolveModalOpen: false,
    selectedComplaint: null,
    resolveData: {
        status: '',
        notes: ''
    },
    
    openResolveModal(complaintId, status, notes, description, orderCode) {
        this.selectedComplaint = { id: complaintId, desc: description, code: orderCode };
        this.resolveData.status = status;
        this.resolveData.notes = notes || '';
        this.resolveModalOpen = true;
    }
}">

    @if(session('success'))
    <div x-data="{ show: true }" x-show="show" class="mb-5 bg-emerald-50 border border-emerald-200 text-emerald-600 px-4 py-3 rounded-xl flex items-center justify-between shadow-sm">
        <div class="flex items-center gap-2 font-bold text-[14px]">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            {{ session('success') }}
        </div>
        <button @click="show = false" class="text-emerald-400 hover:text-emerald-600"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
    </div>
    @endif

    <!-- Statistic Summary -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="glass-container p-4 rounded-2xl flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-slate-100 text-slate-500 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
            </div>
            <div>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wide">Total Tiket</p>
                <h3 class="text-xl font-black text-slate-800">{{ $complaints->total() }}</h3>
            </div>
        </div>
        <div class="glass-container p-4 rounded-2xl flex items-center gap-4 border-l-4 border-amber-400">
            <div class="w-12 h-12 rounded-xl bg-amber-50 border border-amber-100 text-amber-500 flex items-center justify-center">
                <svg class="w-6 h-6 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wide">Menunggu</p>
                <h3 class="text-xl font-black text-slate-800">{{ \App\Models\Complaint::where('status', 'Menunggu')->count() }}</h3>
            </div>
        </div>
    </div>

    <!-- Desktop Table -->
    <div class="hidden md:block glass-container rounded-[28px] p-2 overflow-hidden shadow-[0_8px_30px_rgb(0,0,0,0.04)] mb-8">
        <div class="overflow-x-auto custom-scrollbar pb-2">
            <table class="w-full text-left whitespace-nowrap border-collapse min-w-[1000px]">
                <thead>
                    <tr class="text-[13px] font-bold text-slate-400 uppercase tracking-widest border-b border-white/60">
                        <th class="py-5 px-6 rounded-tl-[24px]">Pelanggan & Order</th>
                        <th class="py-5 px-4 w-1/3">Detail Komplain</th>
                        <th class="py-5 px-4">Tanggal Masuk</th>
                        <th class="py-5 px-4">Status & Tindakan</th>
                        <th class="py-5 px-6 rounded-tr-[24px] text-right">Delete</th>
                    </tr>
                </thead>
                <tbody class="text-[14.5px] font-medium text-slate-600 align-top">
                    @forelse($complaints as $complaint)
                    <tr class="border-b border-white/40 hover:bg-white/50 transition-colors">
                        <td class="py-5 px-6">
                            <span class="font-bold text-slate-800 block text-[15px]">{{ $complaint->order->customer_name }}</span>
                            <a href="{{ route('order.edit', $complaint->order_id) }}" class="text-[12px] font-bold text-blue-500 hover:text-blue-700 bg-blue-50 px-2 py-0.5 rounded-md inline-block mt-1 transition-colors">
                                {{ $complaint->order->order_code }}
                            </a>
                        </td>
                        <td class="py-5 px-4 whitespace-normal">
                            <p class="text-[13px] text-slate-700 leading-relaxed max-w-sm">{{ $complaint->description }}</p>
                            @if($complaint->resolution_notes)
                            <div class="mt-2 text-[12px] p-2 bg-slate-50 border border-slate-100 rounded-lg">
                                <span class="font-bold text-slate-500 block mb-0.5">Catatan/Solusi Admin:</span>
                                {{ $complaint->resolution_notes }}
                            </div>
                            @endif
                        </td>
                        <td class="py-5 px-4 whitespace-nowrap">
                            <span class="text-[13px] text-slate-600">{{ $complaint->created_at->format('d/m/Y H:i') }}</span>
                            <br>
                            <span class="text-[11px] text-slate-400">{{ $complaint->created_at->diffForHumans() }}</span>
                        </td>
                        <td class="py-5 px-4">
                            @php
                                $badgeColor = match($complaint->status) {
                                    'Menunggu' => 'bg-amber-100 text-amber-700',
                                    'Direview' => 'bg-blue-100 text-blue-700',
                                    'Disetujui' => 'bg-emerald-100 text-emerald-700',
                                    'Ditolak' => 'bg-red-100 text-red-700',
                                    'Diganti Uang' => 'bg-purple-100 text-purple-700',
                                    default => 'bg-slate-100 text-slate-700',
                                };
                            @endphp
                            <button @click="openResolveModal({{ $complaint->id }}, '{{ $complaint->status }}', '{{ addslashes($complaint->resolution_notes) }}', '{{ addslashes($complaint->description) }}', '{{ $complaint->order->order_code }}')" 
                                    class="inline-flex items-center justify-between w-36 px-3 py-1.5 rounded-xl text-[12.5px] font-bold {{ $badgeColor }} transition-all hover:scale-105 cursor-pointer shadow-sm border border-black/5">
                                {{ $complaint->status }}
                                <svg class="w-3.5 h-3.5 opacity-60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path></svg>
                            </button>
                        </td>
                        <td class="py-5 px-6 text-right">
                            <form action="{{ route('complaints.destroy', $complaint->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Hapus permanen tiket komplain ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-8 h-8 flex items-center justify-center rounded-xl bg-red-50 text-red-500 hover:bg-red-500 hover:text-white transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-12 text-center text-slate-500">Belum ada keluhan dari pelanggan. Bagus!</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($complaints->hasPages())
        <div class="p-4 border-t border-slate-100">
            {{ $complaints->links('pagination::tailwind') }}
        </div>
        @endif
    </div>

    <!-- Mobile View -->
    <div class="md:hidden flex flex-col gap-4 mb-8">
        @forelse($complaints as $complaint)
        <div class="glass-container rounded-[20px] p-4 shadow-sm border border-slate-100">
            <div class="flex justify-between items-start mb-3 border-b border-slate-100 pb-3">
                <div>
                    <span class="font-bold text-slate-800 text-[14px]">{{ $complaint->order->customer_name }}</span>
                    <p class="text-[11px] text-blue-500 font-bold bg-blue-50 px-2 py-0.5 rounded inline-block mt-0.5">{{ $complaint->order->order_code }}</p>
                </div>
                <div class="text-right flex flex-col gap-2 items-end">
                    <span class="text-[10px] text-slate-400">{{ $complaint->created_at->format('d/m/Y') }}</span>
                    <form action="{{ route('complaints.destroy', $complaint->id) }}" method="POST" onsubmit="return confirm('Hapus?');">
                        @csrf @method('DELETE')
                        <button class="w-6 h-6 rounded bg-red-50 text-red-500 flex items-center justify-center"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></button>
                    </form>
                </div>
            </div>
            
            <p class="text-[12.5px] text-slate-600 mb-3">{{ mb_strimwidth($complaint->description, 0, 150, '...') }}</p>
            
            <button @click="openResolveModal({{ $complaint->id }}, '{{ $complaint->status }}', '{{ addslashes($complaint->resolution_notes) }}', '{{ addslashes($complaint->description) }}', '{{ $complaint->order->order_code }}')" 
                    class="w-full py-2.5 rounded-xl text-[12px] font-bold border-2 {{ $complaint->status === 'Menunggu' ? 'bg-amber-50 text-amber-600 border-amber-200' : 'bg-slate-50 text-slate-700 border-slate-200' }} flex justify-between items-center px-4">
                {{ $complaint->status }}
                <svg class="w-4 h-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path></svg>
            </button>
        </div>
        @empty
        <div class="py-12 text-center glass-container rounded-[20px]">
            <p class="text-slate-500 text-[13px] font-bold">Belum ada keluhan.</p>
        </div>
        @endforelse
    </div>

    <!-- RESOLVE MODAL -->
    <div x-show="resolveModalOpen" x-cloak class="fixed inset-0 z-[120] flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm" @click="resolveModalOpen = false"></div>
        <div x-show="resolveModalOpen" class="relative bg-white w-full max-w-md rounded-[24px] shadow-2xl p-6 z-10 transform overflow-hidden">
            
            <div class="flex justify-between items-center mb-5">
                <h3 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                    <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                     Tinjau Keluhan
                </h3>
                <button type="button" @click="resolveModalOpen = false" class="text-slate-400 bg-slate-100 p-2 rounded-full"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg></button>
            </div>

            <!-- Complaint Detail Highlight -->
            <div class="bg-indigo-50/50 p-4 rounded-xl mb-5 space-y-3">
                <div>
                    <p class="text-[10px] font-black uppercase text-indigo-400 tracking-wider">ORDER</p>
                    <p class="text-[13px] font-bold text-indigo-800" x-text="selectedComplaint?.code"></p>
                </div>
                <div>
                    <p class="text-[10px] font-black uppercase text-indigo-400 tracking-wider">KOMPLAIN PELANGGAN</p>
                    <p class="text-[13px] font-medium text-slate-700 leading-relaxed" x-text="selectedComplaint?.desc"></p>
                </div>
            </div>

            <form :action="`/komplain/${selectedComplaint?.id}/status`" method="POST">
                @csrf
                @method('PATCH')
                
                <div class="mb-4">
                    <label class="block text-[12px] font-bold text-slate-700 mb-2 uppercase tracking-wide">Status Keputusan</label>
                    <div class="grid grid-cols-2 gap-2">
                        <template x-for="st in ['Menunggu', 'Direview', 'Disetujui', 'Ditolak', 'Diganti Uang']">
                            <label class="cursor-pointer">
                                <input type="radio" name="status" :value="st" x-model="resolveData.status" class="peer hidden">
                                <div class="px-3 py-2 border-2 border-slate-100 rounded-xl text-center text-[12px] font-bold text-slate-500 peer-checked:border-indigo-500 peer-checked:bg-indigo-50 peer-checked:text-indigo-700 transition-all" x-text="st"></div>
                            </label>
                        </template>
                    </div>
                </div>

                <div class="mb-5">
                    <label class="block text-[12px] font-bold text-slate-700 mb-2 uppercase tracking-wide">Catatan Admin / Solusi</label>
                    <textarea name="resolution_notes" x-model="resolveData.notes" rows="3" class="w-full border border-slate-200 bg-slate-50 px-4 py-3 rounded-xl outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-500/10 text-[13px]" placeholder="Misal: Sudah diganti dengan cucian gratis..."></textarea>
                </div>

                <button type="submit" class="w-full bg-indigo-500 hover:bg-indigo-600 text-white font-bold py-3.5 rounded-xl shadow-[0_4px_15px_rgba(99,102,241,0.4)] transition-colors text-[14px]">Simpan Perubahan</button>
            </form>
        </div>
    </div>

</div>

@endsection
