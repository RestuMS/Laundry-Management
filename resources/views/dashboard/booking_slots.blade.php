@extends('layouts.dashboard')

@section('title', 'Manajemen Antrian')
@section('header_title', 'Manajemen Antrian & Slot Booking')

@section('content')

<div x-data="antrianApp()" class="space-y-6">

    {{-- ── Stats Cards ─────────────────────────────── --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="glass-card p-5 rounded-[20px]">
            <p class="text-[12px] font-bold text-slate-400 uppercase tracking-wider mb-1">Total Kapasitas Bulan Ini</p>
            <p class="text-2xl font-black text-slate-800">{{ number_format($totalKapasitas, 0, ',', '.') }} <span class="text-base font-bold text-slate-400">kg</span></p>
        </div>
        <div class="glass-card p-5 rounded-[20px]">
            <p class="text-[12px] font-bold text-slate-400 uppercase tracking-wider mb-1">Sudah Dipesan</p>
            <p class="text-2xl font-black text-blue-600">{{ number_format($totalBooked, 0, ',', '.') }} <span class="text-base font-bold text-blue-400">kg</span></p>
        </div>
        <div class="glass-card p-5 rounded-[20px]">
            <p class="text-[12px] font-bold text-slate-400 uppercase tracking-wider mb-1">Slot Penuh</p>
            <p class="text-2xl font-black text-red-500">{{ $slotPenuh }}</p>
        </div>
        <div class="glass-card p-5 rounded-[20px]">
            <p class="text-[12px] font-bold text-slate-400 uppercase tracking-wider mb-1">Slot Ditutup</p>
            <p class="text-2xl font-black text-amber-500">{{ $slotDitutup }}</p>
        </div>
    </div>

    {{-- ── Header Controls ──────────────────────────── --}}
    <div class="glass-card p-5 rounded-[20px] flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center">
                <i class="ph-fill ph-calendar-check text-blue-500 text-xl"></i>
            </div>
            <div>
                <h2 class="text-[16px] font-bold text-slate-800">Kalender Antrian</h2>
                <p class="text-[12px] text-slate-400 font-medium">Atur kapasitas & status buka/tutup tiap hari</p>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            {{-- Month Navigator --}}
            <form method="GET" action="{{ route('booking-slots.index') }}" class="flex items-center gap-2">
                <input type="month" name="month" value="{{ $month }}"
                    class="px-3 py-2 rounded-xl border border-slate-200 text-[13px] font-semibold text-slate-700 focus:ring-2 focus:ring-blue-100 focus:border-blue-400 outline-none"
                    onchange="this.form.submit()">
            </form>

            {{-- Default Capacity Setting --}}
            <button @click="showCapacityModal = true"
                class="px-4 py-2 rounded-xl bg-slate-100 text-slate-700 text-[13px] font-bold hover:bg-slate-200 transition-all flex items-center gap-1.5">
                <i class="ph ph-sliders text-sm"></i> Kapasitas Default
            </button>

            {{-- Bulk Actions --}}
            <div class="flex gap-2">
                <form method="POST" action="{{ route('booking-slots.bulk-toggle') }}"
                    onsubmit="return confirm('Tutup semua slot di bulan {{ \Carbon\Carbon::createFromFormat('Y-m', $month)->locale('id')->isoFormat('MMMM YYYY') }}?')">
                    @csrf
                    <input type="hidden" name="month" value="{{ $month }}">
                    <input type="hidden" name="is_open" value="0">
                    <button type="submit" class="px-3 py-2 rounded-xl bg-red-50 text-red-600 text-[12px] font-bold hover:bg-red-100 transition-all flex items-center gap-1.5">
                        <i class="ph ph-lock text-sm"></i> Tutup Semua
                    </button>
                </form>
                <form method="POST" action="{{ route('booking-slots.bulk-toggle') }}">
                    @csrf
                    <input type="hidden" name="month" value="{{ $month }}">
                    <input type="hidden" name="is_open" value="1">
                    <button type="submit" class="px-3 py-2 rounded-xl bg-emerald-50 text-emerald-600 text-[12px] font-bold hover:bg-emerald-100 transition-all flex items-center gap-1.5">
                        <i class="ph ph-lock-open text-sm"></i> Buka Semua
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- ── Calendar Grid ─────────────────────────────── --}}
    <div class="glass-card p-5 rounded-[20px]">
        {{-- Weekday headers --}}
        <div class="grid grid-cols-7 gap-1 mb-2">
            @foreach(['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'] as $d)
            <div class="text-center text-[11px] font-bold text-slate-400 py-1">{{ $d }}</div>
            @endforeach
        </div>

        {{-- Calendar days --}}
        @php
            $monthStart = \Carbon\Carbon::createFromFormat('Y-m', $month)->startOfMonth();
            $startDow = $monthStart->dayOfWeek; // 0=Sun
            $today = \Carbon\Carbon::today()->toDateString();
            $dayIndex = 0;
        @endphp

        <div class="grid grid-cols-7 gap-1.5">
            {{-- Empty cells before first day --}}
            @for($i = 0; $i < $startDow; $i++)
                <div></div>
            @endfor

            {{-- Day cells --}}
            @foreach($days as $slot)
            @php
                $isToday = $slot->booking_date->toDateString() === $today;
                $isPast  = $slot->booking_date->isPast() && !$isToday;
                $bg = $isPast ? 'bg-slate-50 opacity-60' : ($slot->is_full ? 'bg-red-50 border-red-200' : ($slot->is_open ? 'bg-emerald-50 border-emerald-200' : 'bg-amber-50 border-amber-200'));
                $textColor = $slot->is_full ? 'text-red-700' : ($slot->is_open ? 'text-emerald-700' : 'text-amber-700');
            @endphp
            <button
                @if(!$isPast)
                @click="openSlotModal({{ json_encode(['id' => $slot->id, 'date' => $slot->booking_date->toDateString(), 'date_label' => $slot->booking_date->locale('id')->isoFormat('ddd, D MMM Y'), 'max_capacity_kg' => $slot->max_capacity_kg, 'booked_kg' => $slot->booked_kg, 'is_open' => $slot->is_open, 'notes' => $slot->notes ?? '']) }})"
                @endif
                class="relative aspect-square rounded-xl border p-1.5 text-left transition-all
                    {{ $isToday ? 'ring-2 ring-blue-400 ring-offset-1' : '' }}
                    {{ $bg }}
                    {{ !$isPast ? 'hover:scale-105 hover:shadow-md cursor-pointer' : 'cursor-default' }}"
            >
                <div class="text-[11px] font-black mb-1 {{ $isToday ? 'text-blue-600' : ($isPast ? 'text-slate-400' : $textColor) }}">
                    {{ $slot->booking_date->day }}
                </div>
                @if(!$isPast)
                <div class="text-[9px] font-bold {{ $textColor }} leading-tight">
                    @if(!$slot->is_open)
                        <span>✗ Tutup</span>
                    @elseif($slot->is_full)
                        <span>● Penuh</span>
                    @else
                        <span>{{ $slot->remaining_kg }}kg</span>
                    @endif
                </div>
                {{-- Usage bar --}}
                @if($slot->is_open)
                <div class="mt-1 h-1 bg-white/60 rounded-full overflow-hidden">
                    <div class="h-full rounded-full transition-all
                        {{ $slot->usage_percentage >= 90 ? 'bg-red-500' : ($slot->usage_percentage >= 60 ? 'bg-amber-500' : 'bg-emerald-500') }}"
                        style="width: {{ $slot->usage_percentage }}%"></div>
                </div>
                @endif
                @endif
            </button>
            @endforeach
        </div>

        {{-- Legend --}}
        <div class="flex flex-wrap gap-4 mt-5 pt-4 border-t border-slate-100">
            <div class="flex items-center gap-1.5">
                <div class="w-3.5 h-3.5 rounded-md bg-emerald-100 border border-emerald-200"></div>
                <span class="text-[11px] font-semibold text-slate-500">Tersedia</span>
            </div>
            <div class="flex items-center gap-1.5">
                <div class="w-3.5 h-3.5 rounded-md bg-red-100 border border-red-200"></div>
                <span class="text-[11px] font-semibold text-slate-500">Penuh</span>
            </div>
            <div class="flex items-center gap-1.5">
                <div class="w-3.5 h-3.5 rounded-md bg-amber-100 border border-amber-200"></div>
                <span class="text-[11px] font-semibold text-slate-500">Ditutup</span>
            </div>
            <div class="flex items-center gap-1.5">
                <div class="w-3.5 h-3.5 rounded-md bg-slate-50 border border-slate-200 opacity-60"></div>
                <span class="text-[11px] font-semibold text-slate-500">Sudah Lewat</span>
            </div>
            <div class="flex items-center gap-1.5">
                <div class="w-3.5 h-3.5 rounded-md border-2 border-blue-400"></div>
                <span class="text-[11px] font-semibold text-slate-500">Hari Ini</span>
            </div>
        </div>
    </div>

    {{-- ── Slot Detail Table ─────────────────────────── --}}
    <div class="glass-card rounded-[20px] overflow-hidden">
        <div class="p-5 border-b border-slate-100">
            <h3 class="text-[15px] font-bold text-slate-800">Detail Slot Bulan Ini</h3>
            <p class="text-[12px] text-slate-400 font-medium mt-0.5">Klik baris tanggal untuk edit kapasitas & status</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-[13px]">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100">
                        <th class="text-left py-3 px-5 font-bold text-slate-500 text-[12px] uppercase tracking-wider">Tanggal</th>
                        <th class="text-center py-3 px-4 font-bold text-slate-500 text-[12px] uppercase tracking-wider">Kapasitas</th>
                        <th class="text-center py-3 px-4 font-bold text-slate-500 text-[12px] uppercase tracking-wider">Dipesan</th>
                        <th class="text-center py-3 px-4 font-bold text-slate-500 text-[12px] uppercase tracking-wider">Sisa</th>
                        <th class="text-center py-3 px-4 font-bold text-slate-500 text-[12px] uppercase tracking-wider">Penggunaan</th>
                        <th class="text-center py-3 px-4 font-bold text-slate-500 text-[12px] uppercase tracking-wider">Status</th>
                        <th class="text-center py-3 px-4 font-bold text-slate-500 text-[12px] uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @foreach($days as $slot)
                    @php
                        $isPast = $slot->booking_date->isPast() && $slot->booking_date->toDateString() !== \Carbon\Carbon::today()->toDateString();
                    @endphp
                    <tr class="hover:bg-slate-50 transition-colors {{ $isPast ? 'opacity-50' : '' }}">
                        <td class="py-3 px-5">
                            <span class="font-bold text-slate-700">{{ $slot->booking_date->locale('id')->isoFormat('ddd, D MMM') }}</span>
                            @if($slot->notes)
                            <p class="text-[11px] text-slate-400 font-medium mt-0.5 truncate max-w-[150px]">{{ $slot->notes }}</p>
                            @endif
                        </td>
                        <td class="py-3 px-4 text-center font-bold text-slate-700">{{ $slot->max_capacity_kg }} kg</td>
                        <td class="py-3 px-4 text-center font-bold text-blue-600">{{ $slot->booked_kg }} kg</td>
                        <td class="py-3 px-4 text-center font-bold {{ $slot->remaining_kg <= 0 ? 'text-red-500' : 'text-emerald-600' }}">{{ $slot->remaining_kg }} kg</td>
                        <td class="py-3 px-4">
                            <div class="flex items-center gap-2">
                                <div class="flex-1 h-2 bg-slate-100 rounded-full overflow-hidden">
                                    <div class="h-full rounded-full transition-all
                                        {{ $slot->usage_percentage >= 90 ? 'bg-red-500' : ($slot->usage_percentage >= 60 ? 'bg-amber-500' : 'bg-emerald-500') }}"
                                        style="width: {{ $slot->usage_percentage }}%"></div>
                                </div>
                                <span class="text-[11px] font-bold text-slate-500 w-9 text-right">{{ $slot->usage_percentage }}%</span>
                            </div>
                        </td>
                        <td class="py-3 px-4 text-center">
                            @if(!$slot->is_open)
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-100 text-amber-700">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Tutup
                                </span>
                            @elseif($slot->is_full)
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-red-100 text-red-700">
                                    <span class="w-1.5 h-1.5 rounded-full bg-red-500 animate-pulse"></span> Penuh
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-700">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Tersedia
                                </span>
                            @endif
                        </td>
                        <td class="py-3 px-4 text-center">
                            @if(!$isPast)
                            <button
                                @click="openSlotModal({{ json_encode(['id' => $slot->id, 'date' => $slot->booking_date->toDateString(), 'date_label' => $slot->booking_date->locale('id')->isoFormat('ddd, D MMM Y'), 'max_capacity_kg' => $slot->max_capacity_kg, 'booked_kg' => $slot->booked_kg, 'is_open' => $slot->is_open, 'notes' => $slot->notes ?? '']) }})"
                                class="px-3 py-1.5 rounded-lg bg-blue-50 text-blue-600 text-[12px] font-bold hover:bg-blue-100 transition-all">
                                <i class="ph ph-pencil-simple text-sm"></i> Edit
                            </button>
                            @else
                            <span class="text-[12px] text-slate-300 font-medium">—</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>

{{-- ── Modal: Edit Slot ─────────────────────────── --}}
<div x-show="slotModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display:none;">
    <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm" @click="slotModal = false"></div>
    <div class="relative bg-white rounded-[24px] shadow-2xl w-full max-w-md p-6 z-10"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100">
        <div class="flex items-center justify-between mb-5">
            <div>
                <h3 class="text-[17px] font-bold text-slate-800" x-text="'Slot: ' + editSlot.date_label"></h3>
                <p class="text-[12px] text-slate-400 font-medium mt-0.5">Atur kapasitas dan status slot ini</p>
            </div>
            <button @click="slotModal = false" class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-slate-500 hover:bg-slate-200 transition-colors">
                <i class="ph ph-x text-sm"></i>
            </button>
        </div>

        {{-- Current bookings info --}}
        <div class="bg-blue-50 rounded-2xl p-4 mb-5 flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold text-blue-500 uppercase tracking-wider">Sudah Dipesan</p>
                <p class="text-xl font-black text-blue-700" x-text="editSlot.booked_kg + ' kg'"></p>
            </div>
            <div class="text-right">
                <p class="text-[11px] font-bold text-blue-500 uppercase tracking-wider">Kapasitas Saat Ini</p>
                <p class="text-xl font-black text-blue-700" x-text="editSlot.max_capacity_kg + ' kg'"></p>
            </div>
        </div>

        <div class="space-y-4">
            {{-- Max Capacity --}}
            <div>
                <label class="text-[13px] font-bold text-slate-700 mb-2 block">Kapasitas Maksimal (kg)</label>
                <div class="flex items-center gap-2">
                    <button @click="editSlot.max_capacity_kg = Math.max(editSlot.booked_kg, editSlot.max_capacity_kg - 5)"
                        class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center font-bold text-slate-600 hover:bg-slate-200 transition-colors text-lg">−</button>
                    <input type="number" x-model.number="editSlot.max_capacity_kg" min="1" step="1"
                        class="flex-1 px-4 py-2.5 text-center rounded-xl border border-slate-200 text-[15px] font-black text-slate-800 focus:ring-2 focus:ring-blue-100 focus:border-blue-400 outline-none">
                    <button @click="editSlot.max_capacity_kg += 5"
                        class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center font-bold text-slate-600 hover:bg-slate-200 transition-colors text-lg">+</button>
                </div>
                <p class="text-[11px] text-slate-400 font-medium mt-1.5">
                    Kapasitas minimal = total sudah dipesan (<span x-text="editSlot.booked_kg"></span> kg)
                </p>
            </div>

            {{-- Status Toggle --}}
            <div>
                <label class="text-[13px] font-bold text-slate-700 mb-2 block">Status Slot</label>
                <div class="flex gap-3">
                    <button @click="editSlot.is_open = true"
                        :class="editSlot.is_open ? 'bg-emerald-500 text-white shadow-lg shadow-emerald-500/30' : 'bg-slate-100 text-slate-500'"
                        class="flex-1 py-2.5 rounded-xl text-[13px] font-bold transition-all flex items-center justify-center gap-2">
                        <i class="ph ph-lock-open text-base"></i> Buka
                    </button>
                    <button @click="editSlot.is_open = false"
                        :class="!editSlot.is_open ? 'bg-amber-500 text-white shadow-lg shadow-amber-500/30' : 'bg-slate-100 text-slate-500'"
                        class="flex-1 py-2.5 rounded-xl text-[13px] font-bold transition-all flex items-center justify-center gap-2">
                        <i class="ph ph-lock text-base"></i> Tutup
                    </button>
                </div>
            </div>

            {{-- Notes --}}
            <div>
                <label class="text-[13px] font-bold text-slate-700 mb-2 block">Catatan Internal (opsional)</label>
                <input type="text" x-model="editSlot.notes" placeholder="Misal: Libur nasional, mesin servis..."
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-[13px] font-medium text-slate-700 focus:ring-2 focus:ring-blue-100 focus:border-blue-400 outline-none placeholder-slate-300">
            </div>
        </div>

        <div class="flex gap-3 mt-6">
            <button @click="slotModal = false" class="px-5 py-3 rounded-xl bg-slate-100 text-slate-600 text-[13px] font-bold hover:bg-slate-200 transition-all">
                Batal
            </button>
            <button @click="saveSlot()" :disabled="isSaving"
                class="flex-1 py-3 rounded-xl bg-gradient-to-r from-blue-500 to-indigo-600 text-white text-[14px] font-bold shadow-lg shadow-blue-500/30 hover:-translate-y-0.5 transition-all flex items-center justify-center gap-2 disabled:opacity-50">
                <svg x-show="isSaving" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                <i x-show="!isSaving" class="ph ph-floppy-disk text-base"></i>
                <span x-text="isSaving ? 'Menyimpan...' : 'Simpan Perubahan'"></span>
            </button>
        </div>
    </div>
</div>

{{-- ── Modal: Default Capacity ──────────────────── --}}
<div x-show="showCapacityModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display:none;">
    <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm" @click="showCapacityModal = false"></div>
    <div class="relative bg-white rounded-[24px] shadow-2xl w-full max-w-sm p-6 z-10"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100">
        <div class="flex items-center gap-3 mb-5">
            <div class="w-10 h-10 rounded-xl bg-indigo-50 flex items-center justify-center">
                <i class="ph-fill ph-sliders text-indigo-500 text-xl"></i>
            </div>
            <div>
                <h3 class="text-[16px] font-bold text-slate-800">Kapasitas Harian Default</h3>
                <p class="text-[12px] text-slate-400 font-medium">Berlaku untuk slot baru yang dibuat</p>
            </div>
        </div>

        <form method="POST" action="{{ route('booking-slots.default-capacity') }}">
            @csrf
            <div class="mb-5">
                <label class="text-[13px] font-bold text-slate-700 mb-2 block">Kapasitas Default (kg/hari)</label>
                <input type="number" name="daily_capacity_kg" value="{{ $defaultCapacity }}" min="1" max="9999" step="1" required
                    class="w-full px-4 py-3 text-center rounded-xl border border-slate-200 text-[20px] font-black text-slate-800 focus:ring-2 focus:ring-blue-100 focus:border-blue-400 outline-none">
                <p class="text-[11px] text-slate-400 font-medium mt-1.5">
                    Nilai ini digunakan saat sistem otomatis membuat slot baru. Slot yang sudah ada tidak berubah.
                </p>
            </div>
            <div class="flex gap-3">
                <button type="button" @click="showCapacityModal = false" class="px-5 py-3 rounded-xl bg-slate-100 text-slate-600 text-[13px] font-bold hover:bg-slate-200 transition-all">Batal</button>
                <button type="submit" class="flex-1 py-3 rounded-xl bg-gradient-to-r from-indigo-500 to-purple-600 text-white text-[14px] font-bold shadow-lg hover:-translate-y-0.5 transition-all">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
function antrianApp() {
    return {
        slotModal: false,
        showCapacityModal: false,
        isSaving: false,
        editSlot: {
            id: null,
            date: '',
            date_label: '',
            max_capacity_kg: 50,
            booked_kg: 0,
            is_open: true,
            notes: '',
        },

        openSlotModal(slot) {
            this.editSlot = { ...slot };
            this.slotModal = true;
        },

        async saveSlot() {
            if (this.editSlot.max_capacity_kg < this.editSlot.booked_kg) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Kapasitas Tidak Valid',
                    text: `Kapasitas tidak boleh lebih kecil dari yang sudah dipesan (${this.editSlot.booked_kg} kg).`,
                    confirmButtonColor: '#3b82f6'
                });
                return;
            }

            this.isSaving = true;
            try {
                const res = await fetch(`/antrian/${this.editSlot.id}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'X-HTTP-Method-Override': 'PATCH',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        _method: 'PATCH',
                        max_capacity_kg: this.editSlot.max_capacity_kg,
                        is_open: this.editSlot.is_open ? 1 : 0,
                        notes: this.editSlot.notes,
                    })
                });
                const data = await res.json();
                if (data.success) {
                    this.slotModal = false;
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Slot berhasil diperbarui.',
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => window.location.reload());
                } else {
                    throw new Error(data.message || 'Gagal menyimpan slot');
                }
            } catch (e) {
                Swal.fire({ icon: 'error', title: 'Gagal', text: e.message, confirmButtonColor: '#3b82f6' });
            } finally {
                this.isSaving = false;
            }
        }
    }
}
</script>
@endpush
