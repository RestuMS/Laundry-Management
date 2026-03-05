@extends('layouts.dashboard')

@section('title', 'Pengaturan Toko')
@section('header_title', 'Profil & Pengaturan Toko')

@section('content')

<!-- Custom Configuration Display -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-8" x-data="{ 
    activeTab: 'store',
    showPass: false,
    showPassConf: false
}">
    
    <!-- Tab Controls / Intro Card -->
    <div class="lg:col-span-2 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white/40 backdrop-blur-md p-6 rounded-[20px] border border-white/60 shadow-sm">
        <div>
            <h2 class="text-2xl font-bold text-slate-800 tracking-tight">Kustomisasi Sistem</h2>
            <p class="text-[13px] text-slate-500 font-medium mt-1">Sesuaikan informasi bisnis dan keamanan akun Anda disini.</p>
        </div>
        <div class="flex bg-slate-100/50 p-1.5 rounded-xl border border-white/80 shrink-0">
            <button @click="activeTab = 'store'" :class="{'bg-white text-primary shadow-sm': activeTab === 'store', 'text-slate-500 hover:text-slate-700': activeTab !== 'store'}" class="px-5 py-2 rounded-lg text-[13px] font-bold transition-all relative">
                Toko & Struk
            </button>
            <button @click="activeTab = 'profile'" :class="{'bg-white text-primary shadow-sm': activeTab === 'profile', 'text-slate-500 hover:text-slate-700': activeTab !== 'profile'}" class="px-5 py-2 rounded-lg text-[13px] font-bold transition-all relative">
                Profil Akun
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="lg:col-span-2 bg-green-50/80 backdrop-blur-sm border border-green-200 text-green-600 px-6 py-4 rounded-2xl flex items-center gap-3">
            <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <span class="text-[14px] font-bold">{{ session('success') }}</span>
        </div>
    @endif

    @if($errors->any())
        <div class="lg:col-span-2 bg-red-50/80 backdrop-blur-sm border border-red-200 text-red-600 px-6 py-4 rounded-2xl flex flex-col gap-1">
            <div class="flex items-center gap-3 text-[14px] font-bold text-red-600 mb-1">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Ada beberapa kesalahan input:
            </div>
            <ul class="list-disc list-inside text-[13px] font-semibold text-red-500 ml-6">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Form Section -->
    <div class="lg:col-span-2 glass-card p-6 md:p-8 rounded-[24px]">
        <form action="{{ route('settings.update') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <!-- STORE TAB -->
            <div x-show="activeTab === 'store'" x-transition.opacity.duration.300ms>
                <div class="flex items-center gap-3 mb-8 pb-4 border-b border-slate-100/60">
                    <div class="w-10 h-10 rounded-xl bg-orange-50 text-orange-500 flex items-center justify-center border border-orange-100">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                    </div>
                    <div>
                        <h3 class="text-[16px] font-bold text-slate-800">Detail Laundry</h3>
                        <p class="text-[12px] font-medium text-slate-400">Tampilan Data di Header / Footer Struk Pelanggan</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Store Name -->
                    <div class="space-y-2">
                        <label class="block text-[13px] font-bold text-slate-700">Nama Toko *</label>
                        <input type="text" name="store_name" value="{{ old('store_name', $settings['store_name'] ?? '') }}" required
                            class="w-full px-4 py-3 bg-white border border-slate-200 rounded-xl focus:ring-4 focus:ring-primary/10 focus:border-primary placeholder-slate-400 text-[14px] font-semibold text-slate-700 transition-all shadow-sm outline-none">
                    </div>

                    <!-- Store Phone -->
                    <div class="space-y-2">
                        <label class="block text-[13px] font-bold text-slate-700">Nomor Telepon / WA Utama *</label>
                        <input type="text" name="store_phone" value="{{ old('store_phone', $settings['store_phone'] ?? '') }}" required
                            class="w-full px-4 py-3 bg-white border border-slate-200 rounded-xl focus:ring-4 focus:ring-primary/10 focus:border-primary placeholder-slate-400 text-[14px] font-semibold text-slate-700 transition-all shadow-sm outline-none">
                    </div>

                    <!-- Store Address -->
                    <div class="space-y-2 md:col-span-2">
                        <label class="block text-[13px] font-bold text-slate-700">Alamat Lengkap *</label>
                        <textarea name="store_address" rows="3" required
                            class="w-full px-4 py-3 bg-white border border-slate-200 rounded-xl focus:ring-4 focus:ring-primary/10 focus:border-primary placeholder-slate-400 text-[14px] font-semibold text-slate-700 transition-all shadow-sm outline-none resize-none">{{ old('store_address', $settings['store_address'] ?? '') }}</textarea>
                    </div>

                    <!-- Receipt Footer -->
                    <div class="space-y-2 md:col-span-2">
                        <label class="block text-[13px] font-bold text-slate-700">Catatan Bawah Struk (Footer)</label>
                        <input type="text" name="receipt_footer" value="{{ old('receipt_footer', $settings['receipt_footer'] ?? '') }}"
                            placeholder="Contoh: Barang yang tidak diambil setelah 1 bulan di luar tanggung jawab kami."
                            class="w-full px-4 py-3 bg-white border border-slate-200 rounded-xl focus:ring-4 focus:ring-primary/10 focus:border-primary placeholder-slate-400 text-[14px] font-medium text-slate-700 transition-all shadow-sm outline-none">
                    </div>

                    <!-- Fonnte Token (WA Gateway) -->
                    <div class="space-y-2 md:col-span-2 mt-2 pt-6 border-t border-slate-100">
                        <label class="block text-[13px] font-bold text-slate-700 flex items-center gap-2">
                            Token API Fonnte (WhatsApp Gateway) <span class="bg-indigo-100 text-indigo-600 text-[10px] px-2 py-0.5 rounded-full">Baru</span>
                        </label>
                        <input type="text" name="fonnte_token" value="{{ old('fonnte_token', $settings['fonnte_token'] ?? '') }}"
                            placeholder="Masukkan Kunci API dari fonnte.com untuk notifikasi tagihan otomatis"
                            class="w-full px-4 py-3 bg-white border border-slate-200 rounded-xl focus:ring-4 focus:ring-primary/10 focus:border-primary placeholder-slate-400 text-[14px] font-medium text-slate-700 transition-all shadow-sm outline-none">
                        <p class="text-[11px] text-slate-500 font-medium mt-1">Gunakan token ini untuk mengaktifkan notifikasi resi tagihan otomatis melalui Bot WhatsApp Server.</p>
                    </div>

                    <!-- Auto WA Notification Toggle -->
                    <div class="space-y-2 md:col-span-2 mt-2 pt-6 border-t border-slate-100">
                        <label class="block text-[13px] font-bold text-slate-700 flex items-center gap-2">
                            Notifikasi WhatsApp Otomatis <span class="bg-green-100 text-green-600 text-[10px] px-2 py-0.5 rounded-full">Baru</span>
                        </label>
                        <div class="flex items-center gap-4 bg-slate-50 p-4 rounded-xl border border-slate-100">
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="hidden" name="auto_wa_notification" value="0">
                                <input type="checkbox" name="auto_wa_notification" value="1" class="sr-only peer" {{ old('auto_wa_notification', $settings['auto_wa_notification'] ?? '1') == '1' ? 'checked' : '' }}>
                                <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-100 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-500"></div>
                            </label>
                            <div>
                                <p class="text-[13px] font-bold text-slate-700">Kirim WA otomatis saat status order berubah</p>
                                <p class="text-[11px] text-slate-400 font-medium mt-0.5">Jika diaktifkan, setiap perubahan status order (Diterima → Dicuci → Dikeringkan → dst) akan otomatis mengirim pesan WhatsApp ke pelanggan dengan progress real-time.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Notification Log Preview -->
                    <div class="space-y-2 md:col-span-2 mt-2 pt-6 border-t border-slate-100">
                        <label class="block text-[13px] font-bold text-slate-700 flex items-center gap-2">
                            <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Log Notifikasi Terakhir
                        </label>
                        @php
                            $recentLogs = \App\Models\NotificationLog::latest()->take(5)->get();
                        @endphp
                        @if($recentLogs->count() > 0)
                        <div class="bg-white rounded-xl border border-slate-100 divide-y divide-slate-50 overflow-hidden">
                            @foreach($recentLogs as $log)
                            <div class="px-4 py-3 flex items-center gap-3 text-[12px]">
                                <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0 {{ $log->delivery_status === 'sent' ? 'bg-green-50 text-green-500' : 'bg-red-50 text-red-500' }}">
                                    @if($log->delivery_status === 'sent')
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    @else
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="font-bold text-slate-700 truncate">{{ $log->phone }} — {{ ucfirst($log->type) }}</p>
                                    <p class="text-slate-400 font-medium">Status: {{ $log->status_trigger }} • {{ $log->created_at->diffForHumans() }}</p>
                                </div>
                                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full {{ $log->delivery_status === 'sent' ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600' }}">{{ strtoupper($log->delivery_status) }}</span>
                            </div>
                            @endforeach
                        </div>
                        @else
                        <div class="bg-slate-50 rounded-xl p-6 text-center">
                            <p class="text-[13px] text-slate-400 font-medium">Belum ada notifikasi yang terkirim.</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- PROFILE TAB -->
            <div x-show="activeTab === 'profile'" style="display: none;" x-transition.opacity.duration.300ms>
                <div class="flex items-center gap-3 mb-8 pb-4 border-b border-slate-100/60">
                    <div class="w-10 h-10 rounded-xl bg-primary/10 text-primary flex items-center justify-center border border-primary/20">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    </div>
                    <div>
                        <h3 class="text-[16px] font-bold text-slate-800">Profil & Keamanan Hak Akses</h3>
                        <p class="text-[12px] font-medium text-slate-400">Atur kredensial login sesi Anda saat ini ({{ ucfirst(auth()->user()?->role) }}).</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    
                    <!-- Info Email Blocked -->
                    <div class="space-y-2">
                        <label class="block text-[13px] font-bold text-slate-700">Email Akun (Login)</label>
                        <input type="text" value="{{ auth()->user()?->email }}" disabled
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-[14px] font-bold text-slate-400 cursor-not-allowed shadow-none outline-none">
                        <p class="text-[11px] text-slate-400 font-medium">Email login tidak dapat diubah secara sepihak.</p>
                    </div>

                    <!-- Profile Name -->
                    <div class="space-y-2">
                        <label class="block text-[13px] font-bold text-slate-700">Nama Tampilan</label>
                        <input type="text" name="profile_name" value="{{ old('profile_name', auth()->user()?->name) }}" required
                            class="w-full px-4 py-3 bg-white border border-slate-200 rounded-xl focus:ring-4 focus:ring-primary/10 focus:border-primary placeholder-slate-400 text-[14px] font-semibold text-slate-700 transition-all shadow-sm outline-none">
                    </div>

                    <!-- New Password -->
                    <div class="space-y-2">
                        <label class="block text-[13px] font-bold text-slate-700">Ubah Password Baru</label>
                        <div class="relative">
                            <input :type="showPass ? 'text' : 'password'" name="profile_password" placeholder="Biarkan kosong jika tidak ingin ubah"
                                class="w-full pl-4 pr-12 py-3 bg-white border border-slate-200 rounded-xl focus:ring-4 focus:ring-primary/10 focus:border-primary placeholder-slate-400 text-[14px] font-medium text-slate-700 transition-all shadow-sm outline-none">
                            <button type="button" @click="showPass = !showPass" class="absolute inset-y-0 right-0 pr-4 flex items-center justify-center text-slate-400 hover:text-primary transition-colors focus:outline-none">
                                <svg x-show="!showPass" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                <svg x-show="showPass" style="display: none;" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"></path></svg>
                            </button>
                        </div>
                    </div>

                    <!-- Confirm Password -->
                    <div class="space-y-2">
                        <label class="block text-[13px] font-bold text-slate-700">Konfirmasi Password Baru</label>
                        <div class="relative">
                            <input :type="showPassConf ? 'text' : 'password'" name="profile_password_confirmation" placeholder="Ulangi password baru"
                                class="w-full pl-4 pr-12 py-3 bg-white border border-slate-200 rounded-xl focus:ring-4 focus:ring-primary/10 focus:border-primary placeholder-slate-400 text-[14px] font-medium text-slate-700 transition-all shadow-sm outline-none">
                            <button type="button" @click="showPassConf = !showPassConf" class="absolute inset-y-0 right-0 pr-4 flex items-center justify-center text-slate-400 hover:text-primary transition-colors focus:outline-none">
                                <svg x-show="!showPassConf" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                <svg x-show="showPassConf" style="display: none;" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"></path></svg>
                            </button>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Submit Button Form Level -->
            <div class="mt-10 pt-6 border-t border-slate-100/60 flex justify-end">
                <button type="submit" class="px-8 py-3 rounded-xl bg-primary text-white font-bold text-[14px] hover:bg-[#4A7CE0] transition-colors shadow-[0_8px_20px_rgba(91,141,239,0.3)] hover:shadow-[0_12px_25px_rgba(91,141,239,0.4)] flex items-center gap-2 transform hover:-translate-y-0.5">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                    Simpan Perubahan
                </button>
            </div>

        </form>
    </div>

</div>

@endsection
