@extends('layouts.dashboard')

@section('title', 'Data Pelanggan')
@section('header_title', 'Manajemen Pelanggan')

@section('content')

<!-- Custom Styles -->
<style>
.glass-container {
    background: rgba(255, 255, 255, 0.65);
    backdrop-filter: blur(16px);
    -webkit-backdrop-filter: blur(16px);
    border: 1px solid rgba(255, 255, 255, 0.8);
}
.btn-float {
    transition: transform 0.2s cubic-bezier(0.175, 0.885, 0.32, 1.275), box-shadow 0.2s ease;
}
.btn-float:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 15px -3px rgba(91, 141, 239, 0.4);
}
.custom-scrollbar::-webkit-scrollbar { height: 6px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(143, 184, 255, 0.4); border-radius: 10px; }
.custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #5B8DEF; }

.search-glow:focus-within {
    box-shadow: 0 0 0 4px rgba(91, 141, 239, 0.15);
    border-color: rgba(91, 141, 239, 0.4);
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
</style>

<div x-data="customerManager()">
    
    <!-- Top Bar: Search & Add -->
    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
        
        <!-- Live Search -->
        <form action="{{ route('pelanggan.index') }}" method="GET" class="flex-1 w-full max-w-xl" x-ref="searchForm">
            <div class="w-full bg-white/70 backdrop-blur-md border border-white/80 rounded-[18px] search-glow transition-all shadow-sm flex items-center h-[52px] px-5">
                <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                <input x-model="search" type="text" name="search" @input.debounce.500ms="submitSearch()" value="{{ $search ?? '' }}" placeholder="Ketik nama pelanggan atau telepon..." class="w-full h-full bg-transparent border-none focus:ring-0 text-[14.5px] text-slate-700 placeholder-slate-400 font-medium px-4" />
                @if(request('search'))
                    <a href="{{ route('pelanggan.index') }}" class="text-slate-400 hover:text-red-400 transition-colors focus:outline-none">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </a>
                @endif
            </div>
        </form>

        <!-- Add Button -->
        <button @click="openAddModal()" class="shrink-0 w-full md:w-auto h-[52px] bg-gradient-to-r from-[#6B9DF2] to-[#5B8DEF] hover:bg-[#5A8CE0] text-white px-7 rounded-[18px] flex items-center justify-center gap-2.5 font-bold text-[14.5px] shadow-[0_4px_15px_rgba(107,157,242,0.4)] btn-float relative overflow-hidden">
            <svg class="w-5 h-5 font-bold shadow-sm rounded-full bg-white/20 p-0.5 relative z-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
            <span class="relative z-10">Tambah Pelanggan</span>
        </button>
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

    <!-- Table Section -->
    <div class="glass-container rounded-[24px] overflow-hidden shadow-sm relative mb-8">

        {{-- ===== DESKTOP TABLE (hidden on mobile) ===== --}}
        <div class="hidden lg:block overflow-x-auto custom-scrollbar pb-3 p-2 sm:p-4">
            <table class="w-full text-left whitespace-nowrap border-collapse min-w-[800px]">
                <thead>
                    <tr class="text-[13px] font-bold text-[#64748B] border-b-2 border-slate-100/60 uppercase tracking-wider">
                        <th class="py-4 px-6 pl-8">ID & Nama Pelanggan</th>
                        <th class="py-4 px-4">Kontak / HP</th>
                        <th class="py-4 px-4">Alamat Singkat</th>
                        <th class="py-4 px-4 text-center">Status</th>
                        <th class="py-4 px-6 pr-8 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-[14.5px] font-medium text-slate-600">
                    @forelse($customers as $customer)
                    @php
                        $colors = ['4F46E5', 'EC4899', 'F59E0B', '8B5CF6'];
                        $bgColor = $colors[$customer->id % count($colors)];
                    @endphp
                    <tr class="border-b border-white/50 hover:bg-white/60 hover:-translate-y-[1px] hover:shadow-[0_4px_10px_rgba(0,0,0,0.02)] transition-all group">
                        <td class="py-4 px-6 pl-8">
                            <div class="flex items-center gap-4">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($customer->full_name) }}&background={{ $bgColor }}&color=fff&rounded=true&bold=true" class="w-11 h-11 rounded-full shadow-sm ring-2 ring-white transform transition-transform group-hover:scale-110" alt="Avatar"/>
                                <div>
                                    <div class="font-bold text-slate-800 text-[15px]">{{ $customer->full_name }}</div>
                                    <div class="text-[12px] font-bold text-[#5B8DEF] opacity-80 mt-0.5">CUST-{{ str_pad($customer->id, 4, '0', STR_PAD_LEFT) }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="py-4 px-4">
                            <div class="flex items-center gap-2 text-slate-600 font-semibold">
                                <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                                {{ $customer->phone ?? '-' }}
                            </div>
                        </td>
                        <td class="py-4 px-4">
                            <div class="text-slate-500 max-w-[180px] truncate">{{ $customer->address ?? '-' }}</div>
                        </td>
                        <td class="py-4 px-4 text-center">
                            @if($customer->status == 'Reguler')
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-[10px] text-[12px] font-bold bg-slate-100 text-slate-500"><span class="w-2 h-2 rounded-full bg-slate-400"></span> Reguler</span>
                            @elseif($customer->status == 'Member')
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-[10px] text-[12px] font-bold bg-[#E5F7EA] text-[#48B868]"><span class="w-2 h-2 rounded-full bg-[#48B868] animate-pulse"></span> Member Setia</span>
                            @else
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-[10px] text-[12px] font-bold bg-[#FFF2DE] text-[#F3A73D]"><span class="w-2 h-2 rounded-full bg-[#F3A73D]"></span> VIP Member</span>
                            @endif
                        </td>
                        <td class="py-4 px-6 pr-8 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <button type="button" @click="openEditModal({{ $customer->toJson() }})" class="w-9 h-9 rounded-xl bg-[#F0F5FF] text-[#5B8DEF] hover:bg-[#5B8DEF] hover:text-white border border-[#5B8DEF]/20 transition-all flex items-center justify-center shadow-sm">
                                    <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                </button>
                                <form action="{{ route('pelanggan.destroy', $customer->id) }}" method="POST" onsubmit="return confirm('Hapus pelanggan ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="w-9 h-9 rounded-xl bg-red-50 text-red-400 hover:bg-red-400 hover:text-white border border-red-200 transition-all flex items-center justify-center shadow-sm">
                                        <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-12 text-center text-slate-500">
                            <svg class="w-16 h-16 mx-auto text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                            <span class="block text-[15px] font-bold text-slate-600">Pelanggan tidak ditemukan.</span>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- ===== MOBILE CARD LIST (visible only on mobile/tablet) ===== --}}
        <div class="lg:hidden divide-y divide-slate-100/70">
            @forelse($customers as $customer)
            @php
                $colors = ['4F46E5', 'EC4899', 'F59E0B', '8B5CF6'];
                $bgColor = $colors[$customer->id % count($colors)];
            @endphp
            <div class="flex items-center gap-3 px-4 py-4 hover:bg-white/60 transition-colors">
                {{-- Avatar --}}
                <img src="https://ui-avatars.com/api/?name={{ urlencode($customer->full_name) }}&background={{ $bgColor }}&color=fff&rounded=true&bold=true" class="w-12 h-12 rounded-full shadow-sm ring-2 ring-white shrink-0" alt="Avatar"/>

                {{-- Info --}}
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="font-bold text-slate-800 text-[14px] truncate">{{ $customer->full_name }}</span>
                        @if($customer->status == 'Member')
                            <span class="shrink-0 inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-[#E5F7EA] text-[#48B868]"><span class="w-1.5 h-1.5 rounded-full bg-[#48B868] animate-pulse"></span>Member</span>
                        @elseif($customer->status == 'VIP')
                            <span class="shrink-0 inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-[#FFF2DE] text-[#F3A73D]"><span class="w-1.5 h-1.5 rounded-full bg-[#F3A73D]"></span>VIP</span>
                        @endif
                    </div>
                    <div class="flex items-center gap-3 mt-0.5 flex-wrap">
                        <span class="text-[11px] font-bold text-[#5B8DEF]">CUST-{{ str_pad($customer->id, 4, '0', STR_PAD_LEFT) }}</span>
                        @if($customer->phone)
                        <span class="text-[11px] text-slate-400 font-medium flex items-center gap-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                            {{ $customer->phone }}
                        </span>
                        @endif
                    </div>
                    @if($customer->address)
                    <div class="text-[11px] text-slate-400 font-medium mt-0.5 truncate">📍 {{ $customer->address }}</div>
                    @endif
                </div>

                {{-- Actions --}}
                <div class="flex items-center gap-1.5 shrink-0">
                    <button type="button" @click="openEditModal({{ $customer->toJson() }})" class="w-9 h-9 rounded-xl bg-[#F0F5FF] text-[#5B8DEF] hover:bg-[#5B8DEF] hover:text-white border border-[#5B8DEF]/20 transition-all flex items-center justify-center">
                        <svg class="w-[16px] h-[16px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                    </button>
                    <form action="{{ route('pelanggan.destroy', $customer->id) }}" method="POST" onsubmit="return confirm('Hapus pelanggan ini?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="w-9 h-9 rounded-xl bg-red-50 text-red-400 hover:bg-red-400 hover:text-white border border-red-200 transition-all flex items-center justify-center">
                            <svg class="w-[16px] h-[16px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        </button>
                    </form>
                </div>
            </div>
            @empty
            <div class="py-16 text-center text-slate-400">
                <svg class="w-16 h-16 mx-auto text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                <p class="font-bold text-slate-600">Pelanggan tidak ditemukan.</p>
            </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($customers->hasPages())
        <div class="px-6 py-4 border-t border-white/60">
            {{ $customers->links('pagination::tailwind') }}
        </div>
        @endif
    </div>


    <!-- MODAL TAMBAH/EDIT -->
    <div x-show="modalOpen" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center overflow-auto bg-slate-900/40 backdrop-blur-sm" style="display: none;">
        <div x-show="modalOpen" @click.away="closeModal()" x-transition.opacity class="relative w-full max-w-md bg-white rounded-[24px] shadow-2xl p-8 z-10 mx-4 border border-white/80">
            
            <h3 class="text-2xl font-bold text-slate-800 mb-6" x-text="isEdit ? 'Update Pelanggan' : 'Tambah Pelanggan Baru'"></h3>

            <form :action="formAction" method="POST">
                @csrf
                <template x-if="isEdit">
                    <input type="hidden" name="_method" value="PUT">
                </template>

                <div class="space-y-4 mb-6">
                    <div>
                        <label class="block text-[14px] font-bold text-slate-600 mb-2">Nama Lengkap <span class="text-red-400">*</span></label>
                        <input type="text" name="full_name" x-model="form.full_name" required class="w-full h-12 rounded-[14px] input-cloud px-4 text-[14.5px] font-medium text-slate-700">
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[14px] font-bold text-slate-600 mb-2">No. HP</label>
                            <input type="text" name="phone" x-model="form.phone" class="w-full h-12 rounded-[14px] input-cloud px-4 text-[14.5px] font-medium text-slate-700">
                        </div>
                        <div>
                            <label class="block text-[14px] font-bold text-slate-600 mb-2">Status <span class="text-red-400">*</span></label>
                            <div class="relative">
                                <select name="status" x-model="form.status" required class="w-full h-12 rounded-[14px] input-cloud px-4 text-[14.5px] font-medium text-slate-700 appearance-none cursor-pointer">
                                    <option value="Reguler">Reguler</option>
                                    <option value="Member">Member</option>
                                    <option value="VIP">VIP</option>
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path></svg>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-[14px] font-bold text-slate-600 mb-2">Alamat Singkat</label>
                        <textarea name="address" x-model="form.address" class="w-full h-20 rounded-[14px] input-cloud p-4 text-[14.5px] font-medium text-slate-700 align-top resize-none"></textarea>
                    </div>
                    
                    <div>
                        <label class="block text-[14px] font-bold text-slate-600 mb-2">Catatan Khusus (Opsional)</label>
                        <input type="text" name="notes" x-model="form.notes" placeholder="Misal: Alergi pewangi X..." class="w-full h-12 rounded-[14px] input-cloud px-4 text-[14.5px] font-medium text-slate-700">
                    </div>
                </div>

                <div class="flex gap-3">
                    <button type="button" @click="closeModal()" class="flex-1 h-12 rounded-[14px] bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold text-[14px] transition-colors">Batal</button>
                    <button type="submit" class="flex-1 h-12 rounded-[14px] bg-[#5B8DEF] hover:bg-[#4a7cdc] text-white font-bold text-[14px] shadow-[0_4px_15px_rgba(91,141,239,0.4)] transition-all">Simpan Data</button>
                </div>
            </form>

        </div>
    </div>

</div>

<script>
function customerManager() {
    return {
        search: '{{ $search ?? '' }}',
        modalOpen: false,
        isEdit: false,
        formAction: '{{ route('pelanggan.store') }}',
        form: { id: '', full_name: '', phone: '', address: '', status: 'Reguler', notes: '' },
        
        submitSearch() {
            this.$refs.searchForm.submit();
        },
        openAddModal() {
            this.isEdit = false;
            this.formAction = '{{ route('pelanggan.store') }}';
            this.form = { full_name: '', phone: '', address: '', status: 'Reguler', notes: '' };
            this.modalOpen = true;
        },
        openEditModal(pel) {
            this.isEdit = true;
            this.formAction = `{{ url('pelanggan') }}/${pel.id}`;
            this.form = { ...pel };
            if(!this.form.status) this.form.status = 'Reguler';
            this.modalOpen = true;
        },
        closeModal() {
            this.modalOpen = false;
        }
    }
}
</script>

@endsection
