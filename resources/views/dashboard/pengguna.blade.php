@extends('layouts.dashboard')

@section('title', 'Manajemen Karyawan')
@section('header_title', 'Akun Pengguna')

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

<div x-data="userManager()">
    
    <!-- Top Bar: Search & Add -->
    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
        
        <!-- Live Search -->
        <form action="{{ route('pengguna.index') }}" method="GET" class="flex-1 w-full max-w-xl" x-ref="searchForm">
            <div class="w-full bg-white/70 backdrop-blur-md border border-white/80 rounded-[18px] search-glow transition-all shadow-sm flex items-center h-[52px] px-5">
                <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                <input x-model="search" type="text" name="search" @input.debounce.500ms="submitSearch()" value="{{ $search ?? '' }}" placeholder="Ketik nama karyawan atau email..." class="w-full h-full bg-transparent border-none focus:ring-0 text-[14.5px] text-slate-700 placeholder-slate-400 font-medium px-4" />
                @if(request('search'))
                    <a href="{{ route('pengguna.index') }}" class="text-slate-400 hover:text-red-400 transition-colors focus:outline-none">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </a>
                @endif
            </div>
        </form>

        <!-- Add Button -->
        <button @click="openAddModal()" class="shrink-0 w-full md:w-auto h-[52px] bg-gradient-to-r from-[#6B9DF2] to-[#5B8DEF] hover:bg-[#5A8CE0] text-white px-7 rounded-[18px] flex items-center justify-center gap-2.5 font-bold text-[14.5px] shadow-[0_4px_15px_rgba(107,157,242,0.4)] btn-float relative overflow-hidden">
            <svg class="w-5 h-5 font-bold shadow-sm rounded-full bg-white/20 p-0.5 relative z-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
            <span class="relative z-10">Tambah Akun</span>
        </button>
    </div>

    @if (session('success'))
        <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-6 py-4 rounded-2xl shadow-sm font-semibold flex items-center gap-3">
            <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            {{ session('success') }}
        </div>
    @endif

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
    <div class="glass-container rounded-[24px] p-2 sm:p-4 overflow-hidden shadow-sm relative mb-8">
        
        <div class="overflow-x-auto custom-scrollbar pb-3">
            <table class="w-full text-left whitespace-nowrap border-collapse min-w-[850px]">
                <thead>
                    <tr class="text-[13px] font-bold text-[#64748B] border-b-2 border-slate-100/60 uppercase tracking-wider">
                        <th class="py-4 px-6 pl-8">Pengguna & Email</th>
                        <th class="py-4 px-4 text-center">Hak Akses (Role)</th>
                        <th class="py-4 px-4">Tgl Terdaftar</th>
                        <th class="py-4 px-6 pr-8 text-right">Aksi</th>
                    </tr>
                </thead>

                <tbody class="text-[14.5px] font-medium text-slate-600">
                    @forelse($users as $usr)
                    @php 
                        $colors = ['4F46E5', '0EA5E9', '10B981', 'F59E0B', '8B5CF6'];
                        $bgColor = $colors[$usr->id % count($colors)];
                    @endphp
                    <tr class="border-b border-white/50 hover:bg-white/60 hover:-translate-y-[1px] hover:shadow-[0_4px_10px_rgba(0,0,0,0.02)] transition-all group">
                        
                        <td class="py-4 px-6 pl-8">
                            <div class="flex items-center gap-4">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($usr->name) }}&background={{ $bgColor }}&color=fff&rounded=true&bold=true" class="w-11 h-11 rounded-full shadow-sm ring-2 ring-white transform transition-transform group-hover:scale-110" alt="Avatar"/>
                                <div>
                                    <div class="font-bold text-slate-800 text-[15px]">{{ $usr->name }}</div>
                                    <div class="text-[12.5px] font-medium text-slate-400 mt-0.5">{{ $usr->email }}</div>
                                </div>
                            </div>
                        </td>

                        <td class="py-4 px-4 text-center">
                            @if($usr->role == 'admin')
                            <span class="inline-flex items-center justify-center min-w-[90px] gap-1 px-3 py-1 rounded-full text-[12px] font-bold bg-[#EAF4FF] text-[#5B8DEF] border border-[#5B8DEF]/20 shadow-[0_2px_10px_rgba(91,141,239,0.1)]">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                                Admin
                            </span>
                            @elseif($usr->role == 'owner')
                            <span class="inline-flex items-center justify-center min-w-[90px] gap-1 px-3 py-1 rounded-full text-[12px] font-bold bg-[#FFF2DE] text-[#F59E0B] border border-[#F59E0B]/20">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path></svg>
                                Owner
                            </span>
                            @else
                            <span class="inline-flex items-center justify-center min-w-[90px] px-3 py-1 rounded-full text-[12px] font-bold bg-slate-100 text-slate-500 border border-slate-200">
                                Kasir
                            </span>
                            @endif
                        </td>

                        <td class="py-4 px-4 text-slate-500 font-medium text-[13.5px]">
                            {{ $usr->created_at->format('d M Y') }}
                        </td>

                        <td class="py-4 px-6 pr-8 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <button type="button" @click="openEditModal({{ $usr->toJson() }})" class="w-9 h-9 rounded-xl bg-[#F0F5FF] text-[#5B8DEF] hover:bg-[#5B8DEF] hover:text-white border border-[#5B8DEF]/20 transition-all flex items-center justify-center shadow-sm">
                                    <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                </button>
                                
                                @if(auth()->id() !== $usr->id)
                                <form action="{{ route('pengguna.destroy', $usr->id) }}" method="POST" onsubmit="return confirm('Peringatan: Menghapus akun ini permanen. Lanjutkan?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="w-9 h-9 rounded-xl bg-red-50 text-red-400 hover:bg-red-400 hover:text-white border border-red-200 hover:border-red-400 transition-all flex items-center justify-center shadow-sm">
                                        <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                        
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="py-12 text-center text-slate-500">
                            <svg class="w-16 h-16 mx-auto text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                            <span class="block text-[15px] font-bold text-slate-600">Pengguna tidak ditemukan.</span>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($users->hasPages())
        <div class="px-6 py-4 border-t border-white/60">
            {{ $users->links('pagination::tailwind') }}
        </div>
        @endif

    </div>

    <!-- MODAL TAMBAH/EDIT -->
    <div x-show="modalOpen" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center overflow-auto bg-slate-900/40 backdrop-blur-sm" style="display: none;">
        <div x-show="modalOpen" @click.away="closeModal()" x-transition.opacity class="relative w-full max-w-md bg-white rounded-[24px] shadow-2xl p-8 z-10 mx-4 border border-white/80">
            
            <h3 class="text-2xl font-bold text-slate-800 mb-6" x-text="isEdit ? 'Ubah Akses Pengguna' : 'Tambah Akun Petugas'"></h3>

            <form :action="formAction" method="POST">
                @csrf
                <template x-if="isEdit">
                    <input type="hidden" name="_method" value="PUT">
                </template>

                <div class="space-y-4 mb-6">
                    <div>
                        <label class="block text-[14px] font-bold text-slate-600 mb-2">Nama Pengguna <span class="text-red-400">*</span></label>
                        <input type="text" name="name" x-model="form.name" required class="w-full h-12 rounded-[14px] input-cloud px-4 text-[14.5px] font-medium text-slate-700">
                    </div>
                    
                    <div>
                        <label class="block text-[14px] font-bold text-slate-600 mb-2">Email Valid <span class="text-red-400">*</span></label>
                        <input type="email" name="email" x-model="form.email" required class="w-full h-12 rounded-[14px] input-cloud px-4 text-[14.5px] font-medium text-slate-700">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[14px] font-bold text-slate-600 mb-2">Password <span x-show="!isEdit" class="text-red-400">*</span></label>
                            <input type="password" name="password" :required="!isEdit" :placeholder="isEdit ? 'Isi jika ingin ubah' : 'Minimal 6 digit'" class="w-full h-12 rounded-[14px] input-cloud px-4 text-[14.5px] font-medium text-slate-700">
                        </div>
                        <div>
                            <label class="block text-[14px] font-bold text-slate-600 mb-2">Role <span class="text-red-400">*</span></label>
                            <div class="relative">
                                <select name="role" x-model="form.role" required class="w-full h-12 rounded-[14px] input-cloud px-4 text-[14.5px] font-medium text-slate-700 appearance-none cursor-pointer">
                                    <option value="kasir">Kasir</option>
                                    <option value="admin">Admin</option>
                                    <option value="owner">Owner</option>
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path></svg>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="flex gap-3">
                    <button type="button" @click="closeModal()" class="flex-1 h-12 rounded-[14px] bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold text-[14px] transition-colors">Batal</button>
                    <button type="submit" class="flex-1 h-12 rounded-[14px] bg-[#5B8DEF] hover:bg-[#4a7cdc] text-white font-bold text-[14px] shadow-[0_4px_15px_rgba(91,141,239,0.4)] transition-all">Simpan Akun</button>
                </div>
            </form>

        </div>
    </div>

</div>

<script>
function userManager() {
    return {
        search: '{{ $search ?? '' }}',
        modalOpen: false,
        isEdit: false,
        formAction: '{{ route('pengguna.store') }}',
        form: { id: '', name: '', email: '', role: 'kasir' },
        
        submitSearch() {
            this.$refs.searchForm.submit();
        },
        openAddModal() {
            this.isEdit = false;
            this.formAction = '{{ route('pengguna.store') }}';
            this.form = { name: '', email: '', role: 'kasir' };
            this.modalOpen = true;
        },
        openEditModal(usr) {
            this.isEdit = true;
            this.formAction = `{{ url('pengguna') }}/${usr.id}`;
            this.form = { ...usr };
            this.modalOpen = true;
        },
        closeModal() {
            this.modalOpen = false;
        }
    }
}
</script>

@endsection
