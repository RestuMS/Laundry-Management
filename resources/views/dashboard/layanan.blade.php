@extends('layouts.dashboard')

@section('title', 'Manajemen Layanan')
@section('header_title', 'Layanan Laundry')

@section('content')

<style>
/* Glassmorphism Card Style */
.service-card {
    background: rgba(255, 255, 255, 0.75);
    backdrop-filter: blur(16px);
    border: 1px solid rgba(255, 255, 255, 0.9);
    transition: all 0.3s ease;
}
.service-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 35px -5px rgba(91, 141, 239, 0.2);
    background: rgba(255, 255, 255, 0.9);
}

.icon-container {
    background: linear-gradient(135deg, #EAF4FF 0%, #CFE2FF 100%);
    box-shadow: inset 0 2px 10px rgba(255,255,255,0.7), 0 5px 15px rgba(91,141,239,0.1);
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

<div x-data="serviceManager()">

    <!-- Top Action Bar -->
    <div class="flex flex-col sm:flex-row justify-between items-center mb-8 gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-700 tracking-tight">Daftar Layanan</h2>
            <p class="text-[14px] text-slate-500 font-medium mt-1">Kelola jenis layanan beserta harga untuk pelanggan.</p>
        </div>
        
        <button @click="openAddModal()" class="shrink-0 h-[48px] bg-gradient-to-r from-[#6B9DF2] to-[#5B8DEF] hover:bg-[#5A8CE0] text-white px-6 rounded-[14px] flex items-center gap-2.5 font-bold text-[14px] shadow-[0_4px_15px_rgba(107,157,242,0.4)] transition-transform hover:scale-105">
            <svg class="w-4 h-4 font-bold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"></path></svg>
            Tambah Layanan
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

    <!-- Grid Layout -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 pb-12">

        @forelse($services as $svc)
        <div class="service-card rounded-[24px] p-6 relative flex flex-col items-center text-center group">
            <div class="w-24 h-24 rounded-full icon-container flex items-center justify-center mb-5 border-4 border-white">
                <svg class="w-12 h-12 text-[#5B8DEF] group-hover:scale-110 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path></svg>
            </div>
            
            <h3 class="text-lg font-bold text-slate-700 mb-1">{{ $svc->service_name }}</h3>
            <p class="text-[13px] text-slate-500 font-medium mb-4 line-clamp-2 px-2 h-[40px]">{{ $svc->description ?? 'Tidak ada deskripsi' }}</p>
            
            <div class="mt-auto w-full">
                <div class="text-[20px] font-black text-[#5B8DEF] mb-5">Rp {{ number_format($svc->price,0,',','.') }} <span class="text-[12px] text-slate-400 font-semibold">/ {{ $svc->unit }}</span></div>
                
                <div class="flex gap-3 w-full">
                    <button @click="openEditModal({{ $svc->toJson() }})" class="flex-1 py-2.5 rounded-[12px] bg-[#F0F5FF] text-[#5B8DEF] hover:bg-[#6B9DF2] hover:text-white font-bold text-[13px] border border-[#6B9DF2]/20 transition-colors flex justify-center items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg> Edit
                    </button>
                    <form action="{{ route('layanan.destroy', $svc->id) }}" method="POST" class="flex-1" onsubmit="return confirm('Hapus layanan ini secara permanen?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full py-2.5 rounded-[12px] bg-[#FDE5E5] text-[#ED6A6A] hover:bg-[#ED6A6A] hover:text-white font-bold text-[13px] border border-[#ED6A6A]/20 transition-colors flex justify-center items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg> Hapus
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @empty
            <div class="col-span-1 sm:col-span-2 lg:col-span-3 py-12 text-center text-slate-500 bg-white/50 rounded-[24px] border border-white/80">
                Belum ada data layanan, klik tombol Tambah Layanan di atas.
            </div>
        @endforelse

    </div>

    <!-- ADD / EDIT MODAL -->
    <div x-show="modalOpen" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center overflow-auto bg-slate-900/40 backdrop-blur-sm" style="display: none;">
        <div x-show="modalOpen" @click.away="closeModal()" x-transition.opacity.duration.300ms class="relative w-full max-w-md bg-white rounded-[24px] shadow-2xl p-8 z-10 mx-4 border border-white/80">
            
            <h3 class="text-2xl font-bold text-slate-800 mb-6" x-text="isEdit ? 'Update Layanan' : 'Tambah Layanan Baru'"></h3>

            <form :action="formAction" method="POST">
                @csrf
                <!-- Dynamic Method Spoofer for PUT -->
                <template x-if="isEdit">
                    <input type="hidden" name="_method" value="PUT">
                </template>

                <div class="space-y-4 mb-6">
                    <div>
                        <label class="block text-[14px] font-bold text-slate-600 mb-2">Nama Layanan <span class="text-red-400">*</span></label>
                        <input type="text" name="service_name" x-model="form.service_name" required class="w-full h-12 rounded-[14px] input-cloud px-4 text-[14.5px] font-medium text-slate-700">
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[14px] font-bold text-slate-600 mb-2">Harga (Rp) <span class="text-red-400">*</span></label>
                            <input type="number" name="price" x-model="form.price" required min="0" class="w-full h-12 rounded-[14px] input-cloud px-4 text-[14.5px] font-medium text-slate-700">
                        </div>
                        <div>
                            <label class="block text-[14px] font-bold text-slate-600 mb-2">Satuan (Unit) <span class="text-red-400">*</span></label>
                            <div class="relative">
                                <select name="unit" x-model="form.unit" required class="w-full h-12 rounded-[14px] input-cloud px-4 text-[14.5px] font-medium text-slate-700 appearance-none cursor-pointer">
                                    <option value="Kg">Per Kilo (Kg)</option>
                                    <option value="Pcs">Per Pcs</option>
                                    <option value="Pasang">Per Pasang</option>
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path></svg>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-[14px] font-bold text-slate-600 mb-2">Deskripsi</label>
                        <textarea name="description" x-model="form.description" class="w-full h-20 rounded-[14px] input-cloud p-4 text-[14.5px] font-medium text-slate-700 align-top resize-none"></textarea>
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
function serviceManager() {
    return {
        modalOpen: false,
        isEdit: false,
        formAction: '{{ route('layanan.store') }}',
        form: {
            id: '',
            service_name: '',
            price: '',
            unit: 'Kg',
            description: ''
        },
        openAddModal() {
            this.isEdit = false;
            this.formAction = '{{ route('layanan.store') }}';
            this.form = { service_name: '', price: '', unit: 'Kg', description: '' };
            this.modalOpen = true;
        },
        openEditModal(svc) {
            this.isEdit = true;
            this.formAction = `{{ url('layanan') }}/${svc.id}`;
            this.form = { ...svc };
            this.modalOpen = true;
        },
        closeModal() {
            this.modalOpen = false;
        }
    }
}
</script>

@endsection
