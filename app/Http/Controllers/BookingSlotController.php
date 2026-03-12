<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BookingSlot;
use App\Models\Setting;
use Carbon\Carbon;

class BookingSlotController extends Controller
{
    // ──────────────────────────────────────────────
    // Admin: Halaman Manajemen Antrian
    // ──────────────────────────────────────────────

    public function index(Request $request)
    {
        $month = $request->input('month', Carbon::now()->format('Y-m'));

        try {
            $start = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
            $end   = Carbon::createFromFormat('Y-m', $month)->endOfMonth();
        } catch (\Exception $e) {
            $start = Carbon::now()->startOfMonth();
            $end   = Carbon::now()->endOfMonth();
            $month = $start->format('Y-m');
        }

        $defaultCapacity = (float) Setting::getValue('daily_capacity_kg', 50);

        // Generate semua hari di bulan ini
        $days = collect();
        $current = $start->copy();
        while ($current <= $end) {
            $dateStr = $current->toDateString();
            $slot = BookingSlot::firstOrCreate(
                ['booking_date' => $dateStr],
                ['max_capacity_kg' => $defaultCapacity, 'booked_kg' => 0, 'is_open' => true]
            );
            $days->push($slot);
            $current->addDay();
        }

        $totalKapasitas = $days->sum('max_capacity_kg');
        $totalBooked    = $days->sum('booked_kg');
        $slotPenuh      = $days->filter->is_full->count();
        $slotDitutup    = $days->filter(fn($d) => !$d->is_open)->count();

        return view('dashboard.booking_slots', compact(
            'days', 'month', 'defaultCapacity',
            'totalKapasitas', 'totalBooked', 'slotPenuh', 'slotDitutup'
        ));
    }

    /**
     * Update kapasitas / status buka/tutup sebuah slot
     */
    public function update(Request $request, BookingSlot $bookingSlot)
    {
        $request->validate([
            'max_capacity_kg' => 'required|numeric|min:1|max:9999',
            'is_open'         => 'required|boolean',
            'notes'           => 'nullable|string|max:255',
        ]);

        $bookingSlot->update([
            'max_capacity_kg' => $request->max_capacity_kg,
            'is_open'         => $request->is_open,
            'notes'           => $request->notes,
        ]);

        return response()->json([
            'success'          => true,
            'remaining_kg'     => $bookingSlot->remaining_kg,
            'usage_percentage' => $bookingSlot->usage_percentage,
            'is_full'          => $bookingSlot->is_full,
        ]);
    }

    /**
     * Tutup / buka massal semua slot di bulan tertentu
     */
    public function bulkToggle(Request $request)
    {
        $request->validate([
            'month'   => 'required|date_format:Y-m',
            'is_open' => 'required|boolean',
        ]);

        $start = Carbon::createFromFormat('Y-m', $request->month)->startOfMonth();
        $end   = Carbon::createFromFormat('Y-m', $request->month)->endOfMonth();

        BookingSlot::whereBetween('booking_date', [$start, $end])->update(['is_open' => $request->is_open]);

        return redirect()->back()->with('success', 'Status semua slot bulan ini berhasil diubah.');
    }

    /**
     * Update kapasitas default di Setting
     */
    public function updateDefaultCapacity(Request $request)
    {
        $request->validate([
            'daily_capacity_kg' => 'required|numeric|min:1|max:9999',
        ]);

        Setting::updateOrCreate(
            ['key' => 'daily_capacity_kg'],
            ['value' => $request->daily_capacity_kg]
        );

        return redirect()->back()->with('success', 'Kapasitas harian default berhasil diperbarui.');
    }

    // ──────────────────────────────────────────────
    // Public API: Cek ketersediaan slot untuk pelanggan
    // ──────────────────────────────────────────────

    /**
     * GET /api/booking-slots
     * Returns available slots for next 14 days (for public landing page)
     */
    public function available(Request $request)
    {
        $days = (int) $request->input('days', 14);
        $days = min($days, 30); // max 30 hari ke depan

        $slots = BookingSlot::getAvailableSlots($days);

        $result = $slots->map(function (BookingSlot $slot) {
            return [
                'date'             => $slot->booking_date->toDateString(),
                'date_label'       => $slot->booking_date->locale('id')->isoFormat('ddd, D MMM YYYY'),
                'day_name'         => $slot->booking_date->locale('id')->isoFormat('dddd'),
                'is_open'          => $slot->is_open,
                'is_full'          => $slot->is_full,
                'is_available'     => $slot->is_available,
                'max_capacity_kg'  => $slot->max_capacity_kg,
                'booked_kg'        => $slot->booked_kg,
                'remaining_kg'     => $slot->remaining_kg,
                'usage_percentage' => $slot->usage_percentage,
                'status_label'     => $slot->is_full
                    ? 'Penuh'
                    : ($slot->is_open ? 'Tersedia' : 'Tutup'),
                'status_color'     => $slot->is_full
                    ? 'red'
                    : ($slot->is_open ? 'green' : 'gray'),
            ];
        });

        return response()->json(['slots' => $result]);
    }
}
